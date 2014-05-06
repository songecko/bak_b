<?php

namespace Tresepic\BoprBundle\Controller\Backend;

use DateTime;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Tresepic\BoprBundle\Entity\Manufacturer;
use Symfony\Component\HttpFoundation\Request;

class ManufacturerController extends ResourceController
{
	public function deleteAction(Request $request)
	{
		$manager = $this->getDoctrine()->getManager();
		
		$manufacturer = $this->findOr404($request);
		$products = $manufacturer->getProducts();
		
		foreach($products as $product)
		{
			$product->setDeletedAt(new DateTime('now'));			
		}
		
		$manager->flush();
		
		return parent::deleteAction($request);
	}
}