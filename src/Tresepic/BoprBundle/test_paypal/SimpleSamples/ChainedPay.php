<?php

require_once('..\src\Tresepic\BoprBundle\test_paypal\PPBootStrap.php');
require_once('..\src\Tresepic\BoprBundle\test_paypal\Common\Constants.php');
define("DEFAULT_SELECT", "- Select -");

function getPaypalUrl($amount, $cancelUrl, $returnUrl)
{
	$receiver = array();
	$receiver[0] = new Receiver();
	$receiver[0]->amount = $amount*0.80;
	$receiver[0]->email = "marca@marca.com";
 
	$receiver[1] = new Receiver();
	$receiver[1]->amount = $amount;
	$receiver[1]->email = "alan@puertorico.com";
	$receiver[1]->primary = "true";

	$receiverList = new ReceiverList($receiver);
	
	$payRequest = new PayRequest();
	
	$payRequest->receiverList = $receiverList;

	$requestEnvelope = new RequestEnvelope("en_US");
	$payRequest->requestEnvelope = $requestEnvelope;
	$payRequest->actionType = "PAY";
	$payRequest->cancelUrl = $cancelUrl;
	$payRequest->returnUrl = $returnUrl;
	$payRequest->currencyCode = "USD";
	//->ipnNotificationUrl = "http://replaceIpnUrl.com";

	$service = new AdaptivePaymentsService(Configuration::getSignatureConfig());
	try {
		/* wrap API method calls on the service object with a try catch */
		$response = $service->Pay($payRequest);
	} catch(Exception $ex) {
		require_once '../Common/Error.php';
		exit;
	}
	
	$payKey = $response->payKey;
	$payPalURL = PAYPAL_REDIRECT_URL . '_ap-payment&paykey=' . $payKey;
	
	return $payPalURL;
}