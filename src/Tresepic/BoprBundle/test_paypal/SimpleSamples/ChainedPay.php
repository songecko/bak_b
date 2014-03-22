<?php

require_once('..\src\Tresepic\BoprBundle\test_paypal\PPBootStrap.php');
require_once('..\src\Tresepic\BoprBundle\test_paypal\Common\Constants.php');
define("DEFAULT_SELECT", "- Select -");

function getPaypalUrl($manufacturers, $prices, $fee, $total, $cancelUrl, $returnUrl)
{
	$receiver = array();
	$receiver[0] = new Receiver();
	$receiver[0]->amount = $total;
	$receiver[0]->email = "alan@puertorico.com";
	$receiver[0]->primary = "true";
	
	for($i=1;$i<=count($manufacturers);$i++)
	{
		$receiver[$i] = new Receiver();
		$receiver[$i]->amount = round($prices[$i]*$fee, 2);
		$receiver[$i]->email = $manufacturers[$i];		
	}

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