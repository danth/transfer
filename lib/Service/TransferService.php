<?php
namespace OCA\Transfer\Service;

use OCA\Transfer\Activity\Providers\TransferFailedProvider;
use OCA\Transfer\Activity\Providers\TransferStartedProvider;
use OCA\Transfer\Activity\Providers\TransferSucceededProvider;

use GuzzleHttp\Exception\BadResponseException;
use OC\Files\Filesystem;
use OCP\Activity\IManager;
use OCP\Http\Client\IClientService;
use OCP\Http\Client\LocalServerException;

class TransferService {
	protected $activityManager;
	protected $clientService;

	public function __construct(
		IManager $activityManager,
		IClientService $clientService
	) {
		$this->activityManager = $activityManager;
		$this->clientService = $clientService;
	}

	/**
	 * @return Whether the transfer succeeded.
	 */
	public function transfer(string $userId, string $path, string $url, string $hashAlgo, string $hash) {
		\OC_Util::tearDownFS();
		\OC_Util::setupFS($userId);

		$this->generateStartedEvent($userId, $path, $url);

		$realPath = Filesystem::getView()->getLocalFile($path);

		$client = $this->clientService->newClient();

		try {
			$response = $client->get($url, ["sink" => $realPath, "timeout" => 0]);
		} catch (BadResponseException $exception) {
			// The HTTP request had an unsuccessful response code.
			$this->generateFailedEvent($userId, $path, $url);
			return false;
		} catch (LocalServerException $exception) {
			// The user tried to access `localhost` or similar.
			$this->generateBlockedEvent($userId, $path, $url);
			return false;
		}

		if ($hash == "" || hash_file($hashAlgo, $realPath) == $hash) {
			Filesystem::touch($path);

			$this->generateSucceededEvent($userId, $path, $url);
			return true;
		} else {
			unlink($realPath);

			$this->generateHashFailedEvent($userId, $path, $url);
			return false;
		}
	}

	protected function generateStartedEvent(string $userId, string $path, string $url) {
		$event = $this->activityManager->generateEvent();
		$event->setApp("transfer");
		$event->setType(TransferStartedProvider::TYPE_TRANSFER_STARTED);
		$event->setAffectedUser($userId);
		$event->setSubject(TransferStartedProvider::SUBJECT_TRANSFER_STARTED, ["url" => $url]);
		$this->activityManager->publish($event);
	}

	protected function generateFailedEvent(string $userId, string $path, string $url) {
		$event = $this->activityManager->generateEvent();
		$event->setApp("transfer");
		$event->setType(TransferFailedProvider::TYPE_TRANSFER_FAILED);
		$event->setAffectedUser($userId);
		$event->setSubject(TransferFailedProvider::SUBJECT_TRANSFER_FAILED, ["url" => $url]);
		$this->activityManager->publish($event);
	}

	protected function generateHashFailedEvent(string $userId, string $path, string $url) {
		$event = $this->activityManager->generateEvent();
		$event->setApp("transfer");
		$event->setType(TransferFailedProvider::TYPE_TRANSFER_FAILED);
		$event->setAffectedUser($userId);
		$event->setSubject(TransferFailedProvider::SUBJECT_TRANSFER_HASH_FAILED, ["url" => $url]);
		$this->activityManager->publish($event);
	}

	protected function generateBlockedEvent(string $userId, string $path, string $url) {
		$event = $this->activityManager->generateEvent();
		$event->setApp("transfer");
		$event->setType(TransferFailedProvider::TYPE_TRANSFER_FAILED);
		$event->setAffectedUser($userId);
		$event->setSubject(TransferFailedProvider::SUBJECT_TRANSFER_BLOCKED, ["url" => $url]);
		$this->activityManager->publish($event);
	}

	protected function generateSucceededEvent(string $userId, string $path, string $url) {
		$event = $this->activityManager->generateEvent();
		$event->setApp("transfer");
		$event->setType(TransferSucceededProvider::TYPE_TRANSFER_SUCCEEDED);
		$event->setAffectedUser($userId);
		$event->setSubject(TransferSucceededProvider::SUBJECT_TRANSFER_SUCCEEDED, ["url" => $url]);
		$event->setObject("files", Filesystem::getFileInfo($path)->getId(), $path);
		$this->activityManager->publish($event);
	}
}
