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
    	
       /* $queryBuilder = $this->getCollectionQueryBuilder();

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
    	 
    	$queryBuilder = $this->getCollectionQueryBuilder();
    
    	$queryBuilder = $this->getCollectionQueryBuilder();
    
    	$queryBuilder
    	->innerJoin('product.taxons', 'taxon')
    	->andWhere('taxon = :taxon')
    	->setParameter('taxon', $taxon)
    	;
    	
    	$products = $queryBuilder->getQuery()->getResult();
    	$manufacturerArray = array();
    	//recorro todos los productos creando un array de marcas de productos
    	foreach($products as $product){  
    		//si existe marca en producto 		
			if($product->getManufacturer()){
		    	//si la marca no está en el array, guardo nueva marca
		    	if (!in_array($product->getManufacturer()->getId(), $manufacturerArray))
		    	{
		    		$manufacturerArray[] = $product->getManufacturer()->getId();
		    	}
			}else
			{
				if (!in_array('others', $manufacturerArray))
				{
					$manufacturerArray[] = 'others';
				}
			}
    	}
    	//mezclo array
    	shuffle($manufacturerArray);
    	//recorro el array de marcas y voy llenando el vector de productos
    	$productsRandom = array();
    	foreach($manufacturerArray as $manufactureId)
    	{    	
    		$productsRandomized = array();
    		foreach($products as $product)
    		{
    			//si no productos sin marcas
    			if($product->getManufacturer())
    			{
	    			if($manufactureId == $product->getManufacturer()->getId())
	    			{
	    				$productsRandomized[] = $product;
	    			}
    			}else
    			{
    				if($manufactureId == 'others')
    				{
    					$productsRandomized[] = $product;
    				}
    			}
    		}// end for each products
    		$productsRandom[$manufactureId] = $productsRandomized;

    	}
    //	ldd($productsRandom);
    	return $productsRandom;
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
