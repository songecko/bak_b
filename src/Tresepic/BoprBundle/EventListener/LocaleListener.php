<?php

namespace Tresepic\BoprBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LocaleListener implements EventSubscriberInterface
{
	private $container;
	private $defaultLocale;

	public function __construct(ContainerInterface $container, $defaultLocale = 'en')
	{
		$this->container = $container;
		$this->defaultLocale = $defaultLocale;
	}

	public function onKernelRequest(GetResponseEvent $event)
	{
		if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
			return;
		}
		
		$request = $event->getRequest();
		$request->setLocale($request->getPreferredLanguage(array('en', 'es')));
	}

	public static function getSubscribedEvents()
	{
		return array(
				// must be registered before the default Locale listener
				KernelEvents::REQUEST => array(array('onKernelRequest', 0)),
		);
	}
}