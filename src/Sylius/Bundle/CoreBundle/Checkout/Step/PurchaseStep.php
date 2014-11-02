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

use Payum\Bundle\PayumBundle\Security\TokenFactory;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Security\HttpRequestVerifierInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\FlowBundle\Process\Context\ProcessContextInterface;
use Sylius\Bundle\PaymentsBundle\SyliusPaymentEvents;
use Sylius\Bundle\CoreBundle\Checkout\SyliusCheckoutEvents;
use Sylius\Bundle\CoreBundle\Event\PurchaseCompleteEvent;
use Sylius\Bundle\PayumBundle\Payum\Request\StatusRequest;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sylius\Bundle\PaymentsBundle\Model\PaymentInterface;

require_once dirname(__FILE__).'/../../../../../Tresepic/BoprBundle/test_paypal/SimpleSamples/ChainedPay.php';

class PurchaseStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        
        $order->setState(PaymentInterface::STATE_NEW);
        
        $total = $order->getTotal()/100;
        $cancelUrl = $this->generateUrl('sylius_cart_summary', array(), true);
        $returnUrl = $this->generateUrl('sylius_checkout_forward', array('stepName' => $this->getName()), true);
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
        
        /*foreach($order->getItems() as $item)
        {
        	if(in_array($item->getProduct()->getManufacturer()->getEmail(), $manufacturers))
        	{
        		$prices[$i] += $item->getProduct()->getPrice()/100;
        	}
        	else
        	{
        		$i++;
        		$prices[$i] = $item->getProduct()->getPrice()/100;
        		$manufacturers[$i] = $item->getProduct()->getManufacturer()->getEmail();
        	}
        }*/
        
        $paypalPayKey = getPaypalPayKey($manufacturers, $fee, $total, $cancelUrl, $returnUrl);
        $payPalURL = getPaypalUrl($paypalPayKey);
        
        /*$captureToken = $this->getTokenFactory()->createCaptureToken(
            $order->getPayment()->getMethod()->getGateway(),
            $order,
            'sylius_checkout_forward',
            array('stepName' => $this->getName())
        );*/

        return new RedirectResponse($payPalURL);
    }

    /**
     * {@inheritdoc}
     */
    public function forwardAction(ProcessContextInterface $context)
    {
        /*$token = $this->getHttpRequestVerifier()->verify($this->getRequest());
        //$this->getHttpRequestVerifier()->invalidate($token);

        $status = new StatusRequest($token);
        $this->getPayum()->getPayment($token->getPaymentName())->execute($status);*/

        /** @var OrderInterface $order */
        /*$order = $status->getModel();

        if (!$order instanceof OrderInterface) {
            throw new \RuntimeException(sprintf('Expected order to be set as model but it is %s', get_class($order)));
        }*/
        
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
    	
        $payment = $order->getPayment();
        $previousState = $order->getPayment()->getState();
        $payment->setState(PaymentInterface::STATE_COMPLETED);
        
        if ($previousState !== $payment->getState()) {
            $this->dispatchEvent(
                SyliusPaymentEvents::PRE_STATE_CHANGE,
                new GenericEvent($order->getPayment(), array('previous_state' => $previousState))
            );
        }

        $this->getDoctrine()->getManager()->flush();

        if ($previousState !== $payment->getState()) {
            $this->dispatchEvent(
                SyliusPaymentEvents::POST_STATE_CHANGE,
                new GenericEvent($order->getPayment(), array('previous_state' => $previousState))
            );
        }

        $event = new PurchaseCompleteEvent($order->getPayment());
        $this->dispatchEvent(SyliusCheckoutEvents::PURCHASE_COMPLETE, $event);

        if ($event->hasResponse()) {
            return $event->getResponse();
        }
    	
        return $this->complete();
    }

    /**
     * @return RegistryInterface
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @return TokenFactory
     */
    protected function getTokenFactory()
    {
        return $this->get('payum.security.token_factory');
    }

    /**
     * @return HttpRequestVerifierInterface
     */
    protected function getHttpRequestVerifier()
    {
        return $this->get('payum.security.http_request_verifier');
    }
}
