<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout\Step;

use Sylius\Bundle\CoreBundle\Checkout\SyliusCheckoutEvents;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\OrderBundle\SyliusOrderEvents;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

/**
 * Final checkout step.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FinalizeStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        
        //Shipping events
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_PRE_COMPLETE, $order);
        $this->getManager()->persist($order);
        $this->getManager()->flush();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_COMPLETE, $order);
        
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);
        
        //Finalize forward
        $order->setUser($this->getUser());
        $this->completeOrder($order);
        
        //Purchase display
        $order->setState(PaymentInterface::STATE_NEW);
        
        $total = $order->getTotal()/100;
        $cancelUrl = $this->generateUrl('sylius_cart_summary', array(), true);
        $returnUrl = $this->generateUrl('sylius_checkout_forward', array('stepName' => 'purchase'), true);
        $fee = $this->container->getParameter("tresepic.paypal.fee");
        
        $manufacturers = array();
        
        foreach($order->getItems() as $item)
        {
        	$manufacturerEmail = $item->getProduct()->getManufacturer()->getEmail();
        	$itemPrice = $item->getTotal()/100;
        	 
        	if(!array_key_exists($manufacturerEmail, $manufacturers))
        	{
        		$manufacturers[$manufacturerEmail] = $itemPrice;
        	}
        	else
        	{
        		$manufacturers[$manufacturerEmail] += $itemPrice;
        	}
        }
        
        $paypalPayKey = getPaypalPayKey($manufacturers, $fee, $total, $cancelUrl, $returnUrl);
        
        return $this->renderStep($context, $order, $paypalPayKey);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
    	//$request = $this->getRequest();
    	
        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_INITIALIZE, $order);
        //$order->setTotal(($order->getTotal())+($request->getSession()->get('priceCalculator')));
        
        $order->setUser($this->getUser());

        $this->completeOrder($order);

        return $this->complete();
    }

    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, $paypalPayKey)
    {
    	return $this->renderCheckoutStep("finalize.html.twig", array(
            'context' => $context,
            'order'   => $order,
    		'paypalPayKey' => $paypalPayKey
        ));
    }

    /**
     * Mark the order as completed.
     *
     * @param OrderInterface $order
     */
    protected function completeOrder(OrderInterface $order)
    {
        $this->dispatchCheckoutEvent(SyliusOrderEvents::PRE_CREATE, $order);
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_PRE_COMPLETE, $order);

        $manager = $this->get('sylius.manager.order');
        $manager->persist($order);
        $manager->flush();

        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::FINALIZE_COMPLETE, $order);
        $this->dispatchCheckoutEvent(SyliusOrderEvents::POST_CREATE, $order);
    }
}
