<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Repository;

use Sylius\Bundle\CoreBundle\Model\ProductInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;
use Sylius\Bundle\VariableProductBundle\Doctrine\ORM\VariableProductRepository;
use Proxies\__CG__\Tresepic\BoprBundle\Entity\Product;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;


/**
 * Product repository.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductRepository extends VariableProductRepository
{
    /**
     * Create paginator for products categorized
     * under given taxon.
     *
     * @param TaxonInterface $taxon
     *
     * @return PagerfantaInterface
     */
    public function createByTaxonPaginator(TaxonInterface $taxon)
    {    	
       /* 
        $queryBuilder = $this->getCollectionQueryBuilder();
        
        $queryBuilder
        ->select($this->getAlias(), 'RAND() AS HIDDEN r')
        ->innerJoin('product.taxons', 'taxon') 
        ->andWhere('taxon = :taxon')
        ->setParameter('taxon', $taxon)
        ->orderBy('product.manufacturer, r')
        ;*/
    	//return $this->getPaginator($queryBuilder);
    	
        $products = $this->createByTaxon($taxon);
        
        return new Pagerfanta(new ArrayAdapter($products));        
    }
    
    public function createByTaxon(TaxonInterface $taxon)
    {
    	$queryBuilder = $this->_em->createQueryBuilder()
	    	->select('manufacturer, product, taxon, variant, variantImage')
	    	->from('Tresepic\BoprBundle\Entity\Manufacturer', 'manufacturer')
	    	->leftJoin('manufacturer.products','product')
	    	->innerJoin('product.taxons', 'taxon')
	    	->leftJoin('product.variants','variant')
	    	->leftJoin('variant.images','variantImage')
	    	->andWhere('taxon = :taxon')
	    	->setParameter('taxon', $taxon)
    	;
    	
    	$manufactures = $queryBuilder->getQuery()->getResult();
    	//mezclo array
    	shuffle($manufactures);

    	$products = array();
    	foreach($manufactures as $manufacture)
    	{    	
    		foreach($manufacture->getProducts() as $product)
    		{
    			$products[] = $product;
    		}
    	}
    	
    	return $products;
    }
    /**
     * Create filter paginator.
     *
     * @param array   $criteria
     * @param array   $sorting
     * @param Boolean $deleted
     *
     * @return PagerfantaInterface
     */
    public function createFilterPaginator($criteria = array(), $sorting = array(), $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder()
            ->select('product, variant')
            ->leftJoin('product.variants', 'variant')
            ->leftJoin('product.manufacturer', 'manufacturer')
        ;

        if (!empty($criteria['name'])) {
            $queryBuilder
                ->andWhere('product.name LIKE :name')
                ->setParameter('name', '%'.$criteria['name'].'%')
            ;
        }
        if (!empty($criteria['sku'])) {
            $queryBuilder
                ->andWhere('variant.sku = :sku')
                ->setParameter('sku', $criteria['sku'])
            ;
        }

        if (!empty($criteria['manufacturer'])) {
        	$queryBuilder
        	->andWhere('manufacturer.name LIKE :manufacturer')
        	->setParameter('manufacturer', '%'.$criteria['manufacturer'].'%')
        	;
        }
        
        $price_from = $criteria['price_from']*100;
        $price_to = $criteria['price_to']*100;
        
        if (!empty($price_from)) {
        	$queryBuilder
        	->andWhere('variant.price >= :price_from')
        	->setParameter('price_from', $price_from)
        	;
        }
        if (!empty($price_to)) {
        	$queryBuilder
        	->andWhere('variant.price <= :price_to')
        	->setParameter('price_to', $price_to)
        	;
        }
        
        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

        return $this->getPaginator($queryBuilder);
    }

    /**
     * Get the product data for the details page.
     *
     * @param integer $id
     *
     * @return null|ProductInterface
     */
    public function findForDetailsPage($id)
    {
        $queryBuilder = $this->getQueryBuilder();

        $this->_em->getFilters()->disable('softdeleteable');

        $queryBuilder
            ->leftJoin('variant.images', 'image')
            ->addSelect('image')
            ->andWhere($queryBuilder->expr()->eq('product.id', ':id'))
            ->setParameter('id', $id)
        ;

        $result = $queryBuilder
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $result;
    }

    /**
     * Find X recently added products.
     *
     * @param integer $limit
     *
     * @return ProductInterface[]
     */
    public function findLatest($limit = 10)
    {
        return $this->findBy(array(), array('createdAt' => 'desc'), $limit);
    }
    
    /**
     * Find random added products.
     *
     * @param integer $limit
     *
     * @return ProductInterface[]
     */
    public function findRandom($limit = 10)
    {
    	// @todo Realizar DQL function random http://stackoverflow.com/questions/17463188/selecting-random-db-entry-in-symfony2-getting-an-error
    	//return $this->findBy(array(), array('RAND()' => 'desc'), $limit);
    	$em = $this->getEntityManager();
		$max = $em->createQuery('
			SELECT MAX(q.id) FROM TresepicBoprBundle:Product q
		')
		->getSingleScalarResult();

		return $em->createQuery('
			SELECT q FROM TresepicBoprBundle:Product q
			WHERE q.id >= :rand
			ORDER BY q.id ASC
		')
		->setParameter('rand',rand(0,$max))
		->setMaxResults($limit)
		->getResult();
    }
}
