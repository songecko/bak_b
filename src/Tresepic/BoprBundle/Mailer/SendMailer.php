<?php

namespace Tresepic\BoprBundle\Mailer;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
//use Odiseo\LanBundle\Entity\User as User;

class SendMailer
{
	
	private $message;
	private $container;
	
	public function __construct(Container $container){
		$this->message = \Swift_Message::newInstance();
		$this->container = $container;
	}

	public function sendCostumerMail()
	{
		$fullname = $user->getFullName();
		$email = $user->getMail();
		$view = 'TresepicBoprBundle:Frontend/Mailer:Email.html.twig';
		
		$message = $this->getMessage($view, $email);
		
		$failures = $this->send($message);
		
		return $failures;
	}
	
	protected function send($message)
	{
		$failures = array();
		
		$mailer = $this->container->get('mailer');
		$mailer->send($message, $failures);
		
		// manually flush the queue (because using spool)
		$spool = $mailer->getTransport()->getSpool();
		$transport = $this->container->get('swiftmailer.transport.real');
		$spool->flushQueue($transport);
		
		return $failures;
	}	
	
	
	private function getMessage($view, $emailTo)
	{
		return $this->message
			->setSubject('Servicio al Cliente')
			->setFrom(array('noreply@amigoslan.com' => 'Brands Of Puerto Rico'))
			->setTo($emailTo)
			->setBody(
				$this->container->get('templating')->render($view),
				'text/html'
			);
	}
}