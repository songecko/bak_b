<?php

namespace Tresepic\BoprBundle\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class CostumerFormController
{
	public function indexAction(Request $request)
	{
		$manager = $this->getDoctrine()->getManager();
		
		$this->get('bopr.send.mailer')->sendCostumerMail();
	}
		
}