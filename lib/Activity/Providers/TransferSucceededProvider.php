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

        if ($this->activityManager->isFormattingFilteredObject()) {
            $subject = $l->t("Saved from {url}");
        } else {
            $subject = $l->t("{url} was saved to {file}");
        }

        $subjectParameters = $event->getSubjectParameters();
        $subject = str_replace("{url}", $subjectParameters["url"], $subject);

        $parameters = [];

        if (!$this->activityManager->isFormattingFilteredObject()) {
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
        }

        $event->setRichSubject($subject, $parameters);

        $this->setIcon($event);
        return $event;
    }
}
