<?php
namespace OCA\Transfer\Activity\Settings;

use OCA\Transfer\Activity\Providers\TransferFailedProvider;

use OCP\Activity\ActivitySettings;
use OCP\IL10N;

class TransferFailedSetting extends ActivitySettings {
    private $l;

    public function __construct(IL10N $l) {
        $this->l = $l;
    }

    public function getIdentifier() {
        return TransferFailedProvider::TYPE_TRANSFER_FAILED;
    }

    public function getName() {
        return $this->l->t("An upload by link was unsuccessful");
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
