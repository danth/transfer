<?php
namespace OCA\Transfer\Activity\Providers;

use OCP\Activity\IEvent;

class TransferSucceededProvider extends BaseProvider {
    public const TYPE_TRANSFER_SUCCEEDED = "transfer_succeeded";
    public const SUBJECT_TRANSFER_SUCCEEDED = "transfer_succeeded";

    public function parse($language, IEvent $event, ?IEvent $previousEvent = null) {
        if ($event->getApp() !== "transfer" || $event->getType() !== self::TYPE_TRANSFER_SUCCEEDED) {
            throw new \InvalidArgumentException();
        }

        $l = $this->languageFactory->get("transfer", $language);

        $subject = $l->t("{url} was transferred to {file}");

        $subjectParameters = $event->getSubjectParameters();
        $parameters = [];

        $parameters["file"] = [
            "type" => "file",
            "id" => $event->getObjectId(),
            "name" => basename($event->getObjectName()),
            "path" => trim($event->getObjectName(), "/"),
            "link" => $this->urlGenerator->linkToRouteAbsolute(
                "files.viewcontroller.showFile",
                ["fileid" => $event->getObjectId()]
            )
        ];

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
