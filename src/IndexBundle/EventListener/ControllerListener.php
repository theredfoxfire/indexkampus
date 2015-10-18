<?php
namespace IndexBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use IndexBundle\Twig\SourceCodeExtension;

/**
* Defines the method that 'listens' to the 'kernel.controller' event, which is
* triggered whenever a controller is executed in the application.
*/
class ControllerListener
{
	protected $twigExtension;

	public function __construct(SourceCodeExtension $twigExtension)
	{
		$this->twigExtension = $twigExtension;
	}

	public function registerCurrentController(FilterControllerEvent $event)
	{
		// this check is needed because in SYmfony a request can perform any
		// number of sub-request.
		if ($event->isMasterRequest()) {
			$this->twigExtension->setController($event->getController());
		}
	}
}