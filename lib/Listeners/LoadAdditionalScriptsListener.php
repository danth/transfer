<?php
namespace OCA\Transfer\Listeners;

use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;

class LoadAdditionalScriptsListener implements IEventListener {
	public function handle(Event $event): void {
		Util::addScript("transfer", "transfer-main");
	}
}
