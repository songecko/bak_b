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
    	/*$MailChimp = new MailChimp('fa45ac5e1dbe50997a0b2f475f6400d1-us7');
		print_r($MailChimp->call('lists/list'));die;
		
		$MailChimp = new MailChimp('fa45ac5e1dbe50997a0b2f475f6400d1-us7');
		$result = $MailChimp->call('lists/subscribe', array(
				'id'                => 'd53b936801',
				'email'             => array('email'=>'prueba@bopr.com'),
				'double_optin'      => false,
				'update_existing'   => true,
				'replace_interests' => false,
				'send_welcome'      => false,
		));
		print_r($result);die;*/
        return $this->render('SyliusWebBundle:Frontend/Homepage:main.html.twig');
    }
}
