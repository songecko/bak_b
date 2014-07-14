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
    	
    	if($errors->count() == 0)
    	{
	    	$mailChimp = new MailChimp('fa45ac5e1dbe50997a0b2f475f6400d1-us7');
	    	$result = $mailChimp->call('lists/subscribe', array(
	    			'id'                => 'd53b936801',
	    			'email'             => array('email'=>$email),
	    			'double_optin'      => false,
	    			'update_existing'   => true,
	    			'replace_interests' => false,
	    			'send_welcome'      => false,
	    	));
    	}
    	
    	return $this->render('SyliusWebBundle:Frontend/Homepage:newsletter.html.twig', array(
    		'errors' => $errors
    	));
    }
}
