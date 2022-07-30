<?php
namespace OCA\Transfer\Activity\Providers;

use OCP\Activity\IEvent;

class TransferFailedProvider extends BaseProvider {
    public const TYPE_TRANSFER_FAILED = "transfer_failed";
    public const SUBJECT_TRANSFER_FAILED = "transfer_failed";
    public const SUBJECT_TRANSFER_HASH_FAILED = "transfer_hash_failed";
    public const SUBJECT_TRANSFER_BLOCKED = "transfer_blocked";

    public function parse($language, IEvent $event, ?IEvent $previousEvent = null) {
        if ($event->getApp() !== "transfer" || $event->getType() !== self::TYPE_TRANSFER_FAILED) {
            throw new \InvalidArgumentException();
        }

        $l = $this->languageFactory->get("transfer", $language);

        $subject = "";
        if ($event->getSubject() == self::SUBJECT_TRANSFER_FAILED) {
            $subject = $l->t("Transfer of {url} failed");
        } elseif ($event->getSubject() == self::SUBJECT_TRANSFER_HASH_FAILED) {
            $subject = $l->t("{url} did not match the checksum provided");
        } else {
            $subject = $l->t("Transfer of {url} was blocked");
        }

        $subjectParameters = $event->getSubjectParameters();
        $parameters = [];

        $parameters["url"] = [
            "type" => "highlight",
            "id" => $subjectParameters["url"],
            "name" => $subjectParameters["url"]
        ];

        $this->setIcon($event);
        $this->setSubjects($event, $subject, $parameters);
        return $event;
    }
}
