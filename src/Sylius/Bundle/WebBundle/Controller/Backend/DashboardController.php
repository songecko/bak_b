<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Backend;

use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
/**
 * Backend dashboard controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DashboardController extends Controller
{
    /**
     * Backend dashboard display action.
     */
    public function mainAction()
    {
        $orderRepository = $this->get('sylius.repository.order');
        $userRepository  = $this->get('sylius.repository.user');

        return $this->render('SyliusWebBundle:Backend/Dashboard:main.html.twig', array(
            'orders_count'        => $orderRepository->countBetweenDates(new \DateTime('2013-01-01'), new \DateTime()),
            'orders'              => $orderRepository->findBy(array(), array('updatedAt' => 'desc'), 5),
            'users'               => $userRepository->findBy(array(), array('id' => 'desc'), 5),
            'registrations_count' => $userRepository->countBetweenDates(new \DateTime('2013-01-01'), new \DateTime()),
            'sales'               => $orderRepository->revenueBetweenDates(new \DateTime('2013-01-01'), new \DateTime()),
            'sales_confirmed'     => $orderRepository->revenueBetweenDates(new \DateTime('2013-01-01'), new \DateTime(), OrderInterface::STATE_CONFIRMED),
        ));
    }
    
    public function downloadBackupAction()
    {
    	$basePath = $this->get('kernel')->getRootDir() . '/../web/backups';

    	$finder = new Finder();
    	$finder->files()->in($basePath);
    	$finder->sortByName();
    	
    	$file = null;
    	foreach ($finder as $finderFile) {
    		//recorre hasta agarrar el ultimo
    		$file = $finderFile;
    	} 
    		
    	if($file == null)
    	{
    		throw $this->createNotFoundException();
    	}
    	
    	$fileName = $file->getFilename();
    	$filePath = $basePath.'/'.$fileName;

    	// prepare BinaryFileResponse
    	$response = new BinaryFileResponse($filePath);
    	$response->trustXSendfileTypeHeader();
    	$response->setContentDisposition(
    			ResponseHeaderBag::DISPOSITION_INLINE,
    			$fileName,
    			iconv('UTF-8', 'ASCII//TRANSLIT', $fileName)
    	);
    	
    	return $response;
    }
}
