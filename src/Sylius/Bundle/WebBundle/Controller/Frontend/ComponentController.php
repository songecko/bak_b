<?php

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ComponentController extends Controller
{
	public function topMenuAction()
	{
		$provider = $this->get('sylius.cart_provider');
		$cart = $provider->getCart();
		
		return $this->render('SyliusWebBundle:Frontend/Component:_topMenu.html.twig', array(
				'cart' => $cart
		));
	}
	
    public function menuAction($isMobile = 0)
    {
    	$taxonomyRepository = $this->get('sylius.repository.taxonomy');
    	
    	$taxonomy = $taxonomyRepository->findOneByName('Categories');
    	if(!$taxonomy)
    		throw $this->createNotFoundException();
    	
    	$taxons = $taxonomy->getTaxons();
    	
        return $this->render('SyliusWebBundle:Frontend/Component:_menu.html.twig', array(
        	'taxons' => $taxons,
        	'isMobile' => $isMobile
        ));
    }
    
    public function shoppingCartAction()
    {
    	$provider = $this->get('sylius.cart_provider');
    	
    	$cart = $provider->getCart();
    	
    	return $this->render('SyliusWebBundle:Frontend/Component:_shoppingCart.html.twig', array(
    		'cart' => $cart
    	));
    }
    
    public function sideMenuAction($parent)
    {
    	$repository = $this->get('sylius.repository.taxon');
    	
    	$taxons = $repository->findByParent($parent);
    	
    	return $this->render('SyliusWebBundle:Frontend/Component:_sideMenu.html.twig', array(
    			'taxons' => $taxons
    	));
    }
    
    public function bannerProductShowAction($id)
    {
    	$repository = $this->get('sylius.repository.product');
    	 
    	$product = $repository->find($id);
    	
    	$taxon = $product->getTaxons()->first();
    	
    	return $this->render('SyliusWebBundle:Frontend/Component:_bannerProductShow.html.twig', array(
    			'taxon' => $taxon
    	));
    }
    
    public function breadcrumbProductShowAction($id)
    {
    	$repository = $this->get('sylius.repository.product');
    
    	$product = $repository->find($id);
    	 
    	$taxons = $product->getTaxons();
    	 
    	return $this->render('SyliusWebBundle:Frontend/Component:_breadcrumbProductShow.html.twig', array(
    			'taxons' => $taxons
    	));
    }
}
