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

require_once '..\paypal\adaptivepayments-sdk-php\lib\services\AdaptivePayments\AdaptivePayments.php';
require_once '..\paypal\adaptivepayments-sdk-php\lib\services\AdaptivePayments\AdaptivePaymentsService.php';

use PayRequest;
use Receiver;
use ReceiverList;
use RequestEnvelope;
use AdaptivePayments;
use AdaptivePaymentsService;

class PurchaseStep extends CheckoutStep
{
    /**
     * {@inheritdoc}
     */
    public function displayAction(ProcessContextInterface $context)
    {
        $order = $this->getCurrentCart();
        
        $order->setState(PaymentInterface::STATE_NEW);
        
        $payRequest = new PayRequest();
        
        $receiver = array();
        $receiver[0] = new Receiver();
        $receiver[0]->amount = $order->getTotal()*0.80;
        $receiver[0]->email = "marca@marca.com";
        	
        $receiver[1] = new Receiver();
        $receiver[1]->amount = $order->getTotal();
        $receiver[1]->email = "alan@puertorico.com";
        $receiver[1]->primary = "true";
        
        $receiverList = new ReceiverList($receiver);
        $payRequest->receiverList = $receiverList;
        
        $requestEnvelope = new RequestEnvelope("en_US");
        $payRequest->requestEnvelope = $requestEnvelope;
        $payRequest->actionType = "PAY";
        $payRequest->cancelUrl = $this->generateUrl('sylius_cart_summary', array(), true);
        $payRequest->returnUrl = $this->generateUrl('sylius_checkout_forward', array('stepName' => $this->getName()));
        $payRequest->currencyCode = "USD";
        //->ipnNotificationUrl = "http://replaceIpnUrl.com";
        
        $sdkConfig = array(
        		"mode" => "sandbox",
        		"acct1.UserName" => "dbzgoku86-facilitator_api1.gmail.com",
        		"acct1.Password" => "1394735275",
        		"acct1.Signature" => "AiJMLFrOPiAi6XQQ9A47BCesyg75A7EYmV60WX.-BYrWC9cK11hr3eqe",
        		"acct1.AppId" => "APP-80W284485P519543T"
        );
        
        //define('PP_CONFIG_PATH', '..\paypal\sdk-core-php\tests');
        $adaptivePaymentsService = new AdaptivePaymentsService($sdkConfig);
        try {
        	/* wrap API method calls on the service object with a try catch */
        	$payResponse = $adaptivePaymentsService->Pay($payRequest);
        } catch(\Exception $ex) {
        	echo $ex->getMessage();
        	throw $ex;
        	//exit;
        }
        
        
        $payKey = $payResponse->payKey;
        $payPalURL = 'https://www.sandbox.paypal.com/webscr&cmd=' . '_ap-payment&paykey=' . $payKey;
        
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
        $token = $this->getHttpRequestVerifier()->verify($this->getRequest());
        //$this->getHttpRequestVerifier()->invalidate($token);

        $status = new StatusRequest($token);
        $this->getPayum()->getPayment($token->getPaymentName())->execute($status);

        /** @var OrderInterface $order */
        $order = $status->getModel();

        if (!$order instanceof OrderInterface) {
            throw new \RuntimeException(sprintf('Expected order to be set as model but it is %s', get_class($order)));
        }

        $payment = $order->getPayment();
        $previousState = $order->getPayment()->getState();
        //$status->markSuccess();
        $payment->setState(PaymentInterface::STATE_COMPLETED);
//var_dump($status->getStatus());die;
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
