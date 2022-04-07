<?php
namespace OCA\Transfer\Service;

use GuzzleHttp\Exception\BadResponseException;
use OC\Files\Filesystem;
use OCP\Http\Client\IClientService;
use OCP\Http\Client\LocalServerException;

class TransferService {
	protected $clientService;

	public function __construct(IClientService $clientService) {
		$this->clientService = $clientService;
	}

	/**
	 * @return Whether the transfer succeeded.
	 */
	public function transfer(string $userId, string $path, string $url) {
		\OC_Util::tearDownFS();
		\OC_Util::setupFS($userId);

		$realPath = Filesystem::getView()->getLocalFile($path);

		$client = $this->clientService->newClient();

		try {
			$response = $client->get($url, ["sink" => $realPath, "timeout" => 0]);
		} catch (BadResponseException) {
			// The HTTP request had an unsuccessful response code.
			return false;
		} catch (LocalServerException) {
			// The user tried to access `localhost` or similar.
			return false;
		}

		Filesystem::touch($path);

		return true;
	}
}
