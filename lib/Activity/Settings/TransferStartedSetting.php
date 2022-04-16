<?php
namespace OCA\Transfer\Activity\Settings;

use OCA\Transfer\Activity\Providers\TransferStartedProvider;

use OCP\Activity\ActivitySettings;
use OCP\IL10N;

class TransferStartedSetting extends ActivitySettings {
    private $l;

    public function __construct(IL10N $l) {
        $this->l = $l;
    }

    public function getIdentifier() {
        return TransferStartedProvider::TYPE_TRANSFER_STARTED;
    }

    public function getName() {
        return $this->l->t("A <strong>transfer</strong> was <strong>started</strong>");
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
        return false;
    }

    public function canChangeMail() {
        return true;
    }

    public function isDefaultEnabledMail() {
        return false;
    }
}
