<?php

namespace Tresepic\BoprBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class CostumerController extends Controller
{
	public function indexAction(Request $request)
	{		
		$contact = $request->get('contact');
		$emailTo = $this->container->getParameter('tresepic.contact.mail');
		$this->get('bopr.send.mailer')->sendCostumerMail($contact,$emailTo);
		return JsonResponse::create(array(
				'status' => 'ok'
		));
	}
		
}