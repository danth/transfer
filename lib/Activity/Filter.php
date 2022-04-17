<?php
namespace OCA\Transfer\Activity;

use OCP\Activity\IFilter;
use OCP\IL10N;
use OCP\IURLGenerator;

class Filter implements IFilter {
	private $l;

	private $url;

	public function __construct(IL10N $l, IURLGenerator $url) {
		$this->l = $l;
		$this->url = $url;
	}

	public function getIdentifier() {
		return "transfer";
	}

	public function getName() {
		return $this->l->t("File transfers");
	}

	public function getPriority() {
		return 30;
	}

	public function getIcon() {
		return $this->url->imagePath("transfer", "app-dark.svg");
	}

	public function filterTypes(array $types) {
		return $types;
	}

	public function allowedApps() {
		return ["transfer"];
	}
}
