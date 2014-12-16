<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Drewm\MailChimp;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Frontend homepage controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class HomepageController extends Controller
{
    /**
     * Store front page.
     *
     * @return Response
     */
    public function mainAction(Request $request)
    {
    	$bannerRepository = $this->get('bopr.repository.banner');
    	$testimonialRepository = $this->get('bopr.repository.testimonial');
    	
    	$banner = $bannerRepository->findBy(array(), array('priority' => 'ASC'));
    	$testimonial = $testimonialRepository->findAll();
    	
        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig', array(
    		'banners' => $banner,
        	'testimonials' => $testimonial
    	));
    }
    
    public function masacoteAction(Request $request)
    {
    	$taxonRepository = $this->get('sylius.repository.taxon');
    	
    	$taxon = $taxonRepository->findOneByName('Masacote');
    	if(!$taxon)
    		throw $this->createNotFoundException();
    	
    	$products = $taxon->getProducts();
    	
    	return $this->render('SyliusWebBundle:Frontend/Homepage:masacote.html.twig', array(
    		'products' => $products
    	));
    }
    
    public function yoapoyoajuliaAction(Request $request)
    {
    	$taxonRepository = $this->get('sylius.repository.taxon');
    	 
    	$taxon = $taxonRepository->findOneByName('#yoapoyoajulia');
    	if(!$taxon)
    		throw $this->createNotFoundException();
    	 
    	$products = $taxon->getProducts();
    	 
    	return $this->render('SyliusWebBundle:Frontend/Homepage:yoapoyoajulia.html.twig', array(
    			'products' => $products
    	));
    }
    
    public function subscriptionAction(Request $request)
    {
    	$taxonRepository = $this->get('sylius.repository.taxon');
    	
    	$taxon = $taxonRepository->findOneByName('subscriptions');
    	if(!$taxon)
    		throw $this->createNotFoundException();
    	
    	$products = $taxon->getProducts();
    	
    	return $this->render('SyliusWebBundle:Frontend/Homepage:subscription.html.twig', array(
    		'products' => $products
    	));
    }
    
    public function servicesAction(Request $request)
    {	 
    	return $this->render('SyliusWebBundle:Frontend/Homepage:services.html.twig', array(
    	));
    }
    
    public function servicesSendMailAction(Request $request)
    {
    	$services = $request->get('services');
    	
    	$sendMailer = $this->container->get('bopr.send.mailer');
    	$sendMailer->sendBoprServicesMail($services['email']);
    	
    	return JsonResponse::create(array('status' => 'ok'));
    }
    
    public function cancelPaypalPopupAction(Request $request)
    {
    	return new Response('<script type="text/javascript" charset="utf-8">
			embeddedPPFlow = top.embeddedPPFlow || top.opener.top.embeddedPPFlow;
			embeddedPPFlow.closeFlow();
			embeddedPPFlow.close();
		</script>');
    }
    
    public function returnPaypalPopupAction(Request $request)
    {
    	return new Response('<script type="text/javascript" charset="utf-8">
			top.location = "'.$this->generateUrl('sylius_homepage', array(), UrlGeneratorInterface::ABSOLUTE_URL).'";
		</script>');
    }
    
    public function newsletterAction()
    {
    	$request = $this->getRequest();
    	
    	$newsletter = $request->get('newsletter');
    	
    	$email = $newsletter['email'];
    	
    	$emailConstraint = new EmailConstraint();
    	$emailConstraint->message = 'Email invalido';
    	
    	$errors = $this->get('validator')->validateValue(
    			$email,
    			$emailConstraint
    	);
    	
    	$flash = 'error';
    	$message = 'Invalid email';
    	
    	if($errors->count() == 0)
    	{
	    	$mailChimp = new MailChimp('fa45ac5e1dbe50997a0b2f475f6400d1-us7');
	    	$mailChimp->call('lists/subscribe', array(
	    			'id'                => 'd53b936801',
	    			'email'             => array('email'=>$email),
	    			'double_optin'      => false,
	    			'update_existing'   => true,
	    			'replace_interests' => false,
	    			'send_welcome'      => false,
	    	));
	    	
	    	$message = \Swift_Message::newInstance()
	    	->setSubject('Newsletter')
	    	->setFrom('info@brandsofpuertorico.com')
	    	->setTo($email)
	    	->setBody(
		    			'Dear Newsletter Subscriber,
	
Thank you for subscribing to the Brands of Puerto Rico Newsletter. The premier Newsletter for all information concerning the events, information and bi-monthly activities carried out by Brands of Puerto Rico.
	
We thank you for taking interest in what the Brands of Puerto Rico E-commerce team is planning and coordinating to better help our local brands owners.
						
Your email account will now be receiving bi-weekly reports concerning the available brands, bi monthly activities planned by our Brands relations team and of course any and all other prioritized information.
						
						
Brands of Puerto Rico Team
#BOPR#somosmásque100x35'
	    			);
	    	
	    	$this->get('mailer')->send($message);
	    	
	    	$flash = 'success';
	    	$message = 'Confirmed subscription';
    	}
    	
    	$request->getSession()->getFlashBag()->add(
	    		$flash,
            	$message
    	);
    	
    	return $this->redirect($this->generateUrl('sylius_homepage'));
    }
    
    public function searchAction()
    {
    	$request = $this->getRequest();
    	 
    	$search = $request->get('search');
    	 
    	$query = $search['query'];
    	
    	$finder = $this->container->get('fos_elastica.finder.website.product');
    	
    	$products = $finder->find($query, 100);
    	
    	if(!$query)
    	{
    		$products = null;
    	}
    	
    	return $this->render('SyliusWebBundle:Frontend/Search:result.html.twig', array(
    			'products' => $products
    	));
    }
}
