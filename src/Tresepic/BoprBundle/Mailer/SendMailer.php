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

	public function sendBoprServicesMail($emailFrom)
	{
		$view = 'TresepicBoprBundle:Frontend/Mailer:BoprServicesEmail.html.twig';
	
		$message = $this->message
			->setSubject('Services of Puerto Rico')
			->setFrom(array($emailFrom))
			->setTo($this->container->getParameter('tresepic.contact.mail'))
			->setBody(
				$this->container->get('templating')->render($view, array('emailFrom' => $emailFrom)),
				'text/html'
			);
	
		$failures = $this->send($message);
	
		return $failures;
	}
	
	public function sendCostumerMail($costumer, $emailTo)
	{
		$view = 'TresepicBoprBundle:Frontend/Mailer:Email.html.twig';
		
		$message = $this->getMessage($view, $costumer, $emailTo);
		
		$failures = $this->send($message);
		
		return $failures;
	}
	
	protected function send($message)
	{
		$failures = array();
		
		$mailer = $this->container->get('mailer');
		$mailer->send($message, $failures);
		
	/*	// manually flush the queue (because using spool)
		$spool = $mailer->getTransport()->getSpool();
		$transport = $this->container->get('swiftmailer.transport.real');
		$spool->flushQueue($transport);*/
		
		return $failures;
	}	
	
	
	private function getMessage($view, $costumer, $emailTo)
	{
		$name = $costumer['name'];
		$lastname = $costumer['lastname'];
		$email = $costumer['email'];
		$phone = $costumer['phone'];
		$question = $costumer['question'];
		return $this->message
			->setSubject('Servicio al Cliente')
			->setFrom(array($email => $name))
			->setTo($emailTo)
			->setBody(
				$this->container->get('templating')->render($view, array('costumer' => $costumer)),
				'text/html'
			);
	}
}