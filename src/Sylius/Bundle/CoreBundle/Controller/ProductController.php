<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Product controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductController extends ResourceController
{
    /**
     * List products categorized under given taxon.
     *
     * @param Request $request
     * @param string  $permalink
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function indexByTaxonAction(Request $request, $permalink)
    {
        $taxon = $this->get('sylius.repository.taxon')
            ->findOneByPermalink($permalink);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $paginator = $this
            ->getRepository()
            ->createByTaxonPaginator($taxon)
        ;
        $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());
        
        $products = array();
        for($i=0; $i<$paginator->getNbPages();$i++)
        {
        	$paginator->setCurrentPage($i+1);
        	$products[$i] = $paginator->getCurrentPageResults();
        }

        return $this->render($this->config->getTemplate('indexByTaxon.html'), array(
            'taxon'    => $taxon,
            'productss' => $products,
        ));
    }

    public function indexByTaxonIdAction(Request $request, $id)
    {
        $taxon = $this->get('sylius.repository.taxon')->find($id);

        if (!isset($taxon)) {
            throw new NotFoundHttpException('Requested taxon does not exist.');
        }

        $paginator = $this
            ->getRepository()
            ->createByTaxonPaginator($taxon)
        ;

        $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());
        $paginator->setCurrentPage($request->query->get('page', 1));

        return $this->render($this->config->getTemplate('productIndex.html'), array(
            'taxon'    => $taxon,
            'products' => $paginator,
        ));
    }

    /**
     * Get product history changes.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function historyAction(Request $request)
    {
        $product = $this->findOr404($request);

        $logEntryRepository = $this->get('doctrine')->getManager()->getRepository('Gedmo\Loggable\Entity\LogEntry');

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('history.html'))
            ->setData(array(
                $this->config->getResourceName() => $product,
                'logs'                           => $logEntryRepository->getLogEntries($product)
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * Render product filter form.
     *
     * @param Request $request
     */
    public function filterFormAction(Request $request)
    {
        return $this->render('SyliusWebBundle:Backend/Product:filterForm.html.twig', array(
            'form' => $this->get('form.factory')->createNamed('criteria', 'sylius_product_filter', $request->query->get('criteria'))->createView()
        ));
    }
}
