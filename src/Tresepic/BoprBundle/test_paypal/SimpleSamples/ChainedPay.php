<?php

require_once dirname(__FILE__).'/../PPBootStrap.php';
require_once dirname(__FILE__).'/../Common/Constants.php';
define("DEFAULT_SELECT", "- Select -");

function getPaypalUrl($payKey)
{
	$payPalURL = PAYPAL_REDIRECT_URL . '_ap-payment&paykey=' . $payKey . '&senderOptions.referrerCode=' . BN_CODE;
	
	return $payPalURL;
}

function getPaypalPayKey($manufacturers, $fee, $total, $cancelUrl, $returnUrl)
{
	$receiver = array();
	$receiver[0] = new Receiver();
	$receiver[0]->amount = $total;
	$receiver[0]->email = "alanmikahil.taveras@gmail.com";
	$receiver[0]->primary = "true";
	
	$i=1;
	foreach($manufacturers as $manufacturer => $price)
	{
		$receiver[$i] = new Receiver();
		$receiver[$i]->amount = round($price*$fee, 2);
		$receiver[$i]->email = $manufacturer;
		$i++;
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
		//ldd($response->error);
	} catch(Exception $ex) {
		ldd($ex);
		require_once '../Common/Error.php';
		exit;
	}
	
	return $response->payKey;
}