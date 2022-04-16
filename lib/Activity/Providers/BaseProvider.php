<?php
namespace OCA\Transfer\Activity\Providers;

use OCP\Activity\IEvent;
use OCP\Activity\IProvider;
use OCP\IURLGenerator;
use OCP\L10N\IFactory;

abstract class BaseProvider implements IProvider {
    protected $languageFactory;
    protected $urlGenerator;

    public function __construct(IFactory $languageFactory, IURLGenerator $urlGenerator) {
        $this->languageFactory = $languageFactory;
        $this->urlGenerator = $urlGenerator;
    }

    protected function setIcon(IEvent $event) {
        // TODO: Respect activityManager->getRequirePNG()
        $event->setIcon(
            $this->urlGenerator->getAbsoluteUrl(
                $this->urlGenerator->imagePath("transfer", "app.svg")
            )
        );
    }

    protected function setSubjects(IEvent $event, $subject, array $parameters) {
        $placeholders = $replacements = [];
        foreach ($parameters as $placeholder => $parameter) {
            $placeholders[] = '{' . $placeholder . '}';
            if ($parameter['type'] === 'file') {
                $replacements[] = $parameter['path'];
            } else {
                $replacements[] = $parameter['name'];
            }
        }

        $event->setParsedSubject(str_replace($placeholders, $replacements, $subject));
        $event->setRichSubject($subject, $parameters);
    }
}
