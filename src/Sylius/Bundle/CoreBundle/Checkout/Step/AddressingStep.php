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
use Symfony\Component\Form\FormInterface;

/**
 * The addressing step of checkout.
 * User enters the shipping and shipping address.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class AddressingStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        
        $manufacturers = array();
        $i = 0;
        
        foreach($order->getItems() as $item)
        {
        	$manufacturerEmail = $item->getProduct()->getManufacturer()->getEmail();
        	$itemSale = $item->getProduct()->getName().' cantidad: '.$item->getQuantity()."\n";
        	
        	if(!array_key_exists($manufacturerEmail, $manufacturers))
        	{
        		$manufacturers[$manufacturerEmail] = $itemSale;
        	}
        	else
        	{
        		$manufacturers[$manufacturerEmail] .= $itemSale;
        	}
        }
        
        foreach($manufacturers as $manufacturer => $item)
        {
        	$message = \Swift_Message::newInstance()
	    	->setSubject('Venta')
	    	->setFrom('info@brandsofpuertorico.com')
	    	->setTo($manufacturer)
	    	->setBody(
		    			'Saludos,

Queremos felicitarte porque has vendido tus productos en Brands of Puerto Rico. Estamos muy emocionados y orgullosos de ustedes y todas las marcas que componen nuestra iniciativa. Esto demuestra que nuestro trabajo junto a sus maravillosos productos SI va a rendir frutos. Entiendo que ya recibieron el pago de Paypal, por favor confirmen.

Esto es una oportunidad para darle a este cliente una experiencia extraordinaria y más aún, demostrarle que en Puerto Rico las cosas se hacen bien y de calidad. Les pedimos que nos ayuden a sacarle provecho a esta oportunidad. Queremos recolectar sus productos lo más pronto posible para enviarlos. Además de sus productos, si tienen algún material promocional está más que bienvenido. 

Gracias por unirte en este emprendimiento y creer en nosotros. Juntos vamos a lograr cosas grandes y demostrar que somos más que 100 x 35, este es solo el comienzo. Confiamos plenamente en ustedes, sus marcas y sus productos, por eso damos y vamos a seguir dando todo nuestro esfuerzo para que la gente los conozca. Para nosotros ustedes son una inspiración para hacer las cosas mejor. 

El producto vendido fue: 

'.$item.' 


Cualquier duda saben que estamos a la orden. Esto es un trabajo en equipo, sin ustedes no existiría Brands of Puerto Rico.

¡De nuevo, gracias de parte de todo nuestro equipo!'
	    			);
        	
        	$this->get('mailer')->send($message);
        }
        
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_INITIALIZE, $order);

        $form = $this->createCheckoutAddressingForm($order);

        return $this->renderStep($context, $order, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        $request = $this->getRequest();

        $order = $this->getCurrentCart();
        $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_INITIALIZE, $order);

        $form = $this->createCheckoutAddressingForm($order);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_PRE_COMPLETE, $order);

            $this->getManager()->persist($order);
            $this->getManager()->flush();

            $this->dispatchCheckoutEvent(SyliusCheckoutEvents::ADDRESSING_COMPLETE, $order);

            return $this->complete();
        }

        return $this->renderStep($context, $order, $form);
    }

    protected function renderStep(ProcessContextInterface $context, OrderInterface $order, FormInterface $form)
    {
    	if($this->getRequest()->isXmlHttpRequest())
    	{
    		return $this->render('SyliusWebBundle:Frontend/Checkout/Step:addressingAjax.html.twig', array(
    				'order'   => $order,
    				'form'    => $form->createView(),
    				'context' => $context
    		));
    	}
    	
        return $this->render('SyliusWebBundle:Frontend/Checkout/Step:addressing.html.twig', array(
            'order'   => $order,
            'form'    => $form->createView(),
            'context' => $context
        ));
    }

    protected function createCheckoutAddressingForm(OrderInterface $order)
    {
        return $this->createForm('sylius_checkout_addressing', $order);
    }
}
