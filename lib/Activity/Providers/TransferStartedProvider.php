<?php
namespace OCA\Transfer\Activity\Providers;

use OCP\Activity\IEvent;

class TransferStartedProvider extends BaseProvider {
    public const TYPE_TRANSFER_STARTED = "transfer_started";
    public const SUBJECT_TRANSFER_STARTED = "transfer_started";

    public function parse($language, IEvent $event, ?IEvent $previousEvent = null) {
        if ($event->getApp() !== "transfer" || $event->getType() !== self::TYPE_TRANSFER_STARTED) {
            throw new \InvalidArgumentException();
        }

        $l = $this->languageFactory->get("transfer", $language);
        $subject = $l->t("Transfer of {url} started");

        $subjectParameters = $event->getSubjectParameters();
        $subject = str_replace("{url}", $subjectParameters["url"], $subject);
        $event->setRichSubject($subject, []);

        $this->setIcon($event);
        return $event;
    }
}
