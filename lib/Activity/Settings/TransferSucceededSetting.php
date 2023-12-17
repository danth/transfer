<?php
namespace OCA\Transfer\Activity\Settings;

use OCA\Transfer\Activity\Providers\TransferSucceededProvider;

use OCP\Activity\ActivitySettings;
use OCP\IL10N;

class TransferSucceededSetting extends ActivitySettings {
    private $l;

    public function __construct(IL10N $l) {
        $this->l = $l;
    }

    public function getIdentifier() {
        return TransferSucceededProvider::TYPE_TRANSFER_SUCCEEDED;
    }

    public function getName() {
        return $this->l->t("An upload by link was successful");
    }

    public function getGroupIdentifier() {
        return "files";
    }

    public function getGroupName() {
        return $this->l->t("Files");
    }

    public function getPriority() {
        return 30;
    }

    public function canChangeNotification() {
        return true;
    }

    public function isDefaultEnabledNotification() {
        return true;
    }

    public function canChangeMail() {
        return true;
    }

    public function isDefaultEnabledMail() {
        return false;
    }
}
