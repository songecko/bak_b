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
    public function mainAction()
    {
    	$finder = $this->container->get('fos_elastica.finder.website.product');
    	
    	$products = $finder->find('book');
    	
    	/*foreach($productSet as $product)
    	{
    		ld($product->getData());
    	}
    	die;*/
    	ldd($products);
    	
        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig');
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
}
