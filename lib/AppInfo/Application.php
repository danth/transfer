<?php
namespace OCA\Transfer\AppInfo;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\Transfer\Listeners\LoadAdditionalScriptsListener;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
	public function __construct(array $urlParams = []) {
		parent::__construct("transfer", $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerEventListener(LoadAdditionalScriptsEvent::class, LoadAdditionalScriptsListener::class);
	}

	public function boot(IBootContext $context): void {
	}
}
