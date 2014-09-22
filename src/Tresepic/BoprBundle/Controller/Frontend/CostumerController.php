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
		return JsonResponse::create(array(
				'status' => $emailTo
		));
		$contact = $request->get('contact'); var_dump($contact);
		$emailTo = $this->container->getParameter('tresepic.contact.mail');
		$this->get('bopr.send.mailera')->sendCostumerMail($contact,$emailTo);
	}
		
}