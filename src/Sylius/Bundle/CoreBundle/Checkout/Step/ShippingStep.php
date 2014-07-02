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

use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\CoreBundle\Checkout\SyliusCheckoutEvents;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Symfony\Component\Form\FormInterface;

require_once dirname(__FILE__).'/../../../../../Tresepic/BoprBundle/usps/PriceCalculator.php';

/**
 * The shipping step of checkout.
 *
 * Based on the user address, we present the available shipping methods,
 * and ask him to select his preferred one.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingStep extends CheckoutStep
{
    /**
     * @var null|ZoneInterface
     */
    private $zone;

    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        //$this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);

        //$form = $this->createCheckoutShippingForm($order);

        /*if (null === $this->zone) {
            return $this->proceed($context->getPreviousStep()->getName());
        }*/
		
        $country = $order->getShippingAddress()->getCountry()->getName();
        $postCode = $order->getShippingAddress()->getPostcode();
        
        $totalPounds = 0;
        $totalWidth = 0;
        $totalLength = 0;
        $totalHeight = 0;
        
        foreach($order->getItems() as $item)
        {
        	$totalPounds += ($item->getQuantity()) * ($item->getProduct()->getMasterVariant()->getWeight());
        	$totalWidth += ($item->getQuantity()) * ($item->getProduct()->getMasterVariant()->getWidth());
        	$totalLength += ($item->getQuantity()) * ($item->getProduct()->getMasterVariant()->getDepth());
        	$totalHeight += ($item->getQuantity()) * ($item->getProduct()->getMasterVariant()->getHeight());
        }
        
        $totalOunces = $totalPounds*16;
        
        $priceCalculator = USPSParcelRate($totalPounds, $totalOunces, 'REGULAR', $postCode, $country, $totalWidth, $totalLength, $totalHeight);
        
        $session = $this->get('session');
        $session->set('priceCalculator', $priceCalculator*100);
        
        return $this->renderStep($context, $order/*, $form*/);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        $order = $this->getCurrentCart();
        //$this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_INITIALIZE, $order);

        //$form = $this->createCheckoutShippingForm($order);

        /*if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_PRE_COMPLETE, $order);
  */
            
            $this->getManager()->persist($order);
            $this->getManager()->flush();

    //        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::SHIPPING_COMPLETE, $order);

            return $this->complete();
      //  }

        //return $this->renderStep($context, $order/*, $form*/);
    }

    protected function renderStep(ProcessContextInterface $context, OrderInterface $order/*, FormInterface $form*/)
    {
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:shipping.html.twig', array(
            'order'   => $order,
            //'form'    => $form->createView(),
            'context' => $context,
        ));
    }

    /*protected function createCheckoutShippingForm(OrderInterface $order)
    {
        $this->zone = $this->getZoneMatcher()->match($order->getShippingAddress());

        if (null === $this->zone) {
            $this->get('session')->getFlashBag()->add('error', 'sylius.checkout.shipping.error');
        }

        return $this->createForm('sylius_checkout_shipping', $order, array(
            'criteria' => array('zone' => $this->zone)
        ));
    }*/
}
