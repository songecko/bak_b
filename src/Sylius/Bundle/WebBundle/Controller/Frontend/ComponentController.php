<?php

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ComponentController extends Controller
{
    public function menuAction()
    {
    	$repository = $this->get('sylius.repository.taxon');
    	
    	$taxons = $repository->findByLevel(1);
    	
        return $this->render('SyliusWebBundle:Frontend/Component:_menu.html.twig', array(
        	'taxons' => $taxons
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
}
