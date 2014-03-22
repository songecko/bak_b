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
        $prices = array();
        $i = 0;
        
        foreach($order->getItems() as $item)
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
        }
        
        $payPalURL = getPaypalUrl($manufacturers, $prices, $fee, $total, $cancelUrl, $returnUrl);
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
