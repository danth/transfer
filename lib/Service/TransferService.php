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
	 * @param string $url URL of a remote file to determine the size of.
	 * @return Whether the request succeeded, and the size of the file if known.
	 */
	public function getSize(string $url) {
		$client = $this->clientService->newClient();
		
		try {
			$response = $client->head($url);

		} catch (BadResponseException $error) {
			// The HTTP request had an unsuccessful response code.
			
			/* 405 means that HEAD requests are not supported by the remote server.
			 * If we receive a 405, the request is marked as a success because this
			 * does not indicate that the actual download will fail.
			 */
			$response = $error->getResponse();
			$success = $response->getStatusCode() == 405;

			return [$success, -1];

		} catch (LocalServerException) {
			// The user tried to access `localhost` or similar.
			return [false, -1];
		}

		$length = $response->getHeader("Content-Length");
		return [true, $length];
	}

	/**
	 * @return Whether the transfer succeeded, and the actual size of the
	 * transferred file.
	 */
	public function transfer(string $userId, string $path, string $url) {
		\OC_Util::tearDownFS();
		\OC_Util::setupFS($userId);

		$realPath = Filesystem::getView()->getLocalFile($path);

		$client = $this->clientService->newClient();

		try {
			$response = $client->get($url, ["sink" => $realPath]);
		} catch (BadResponseException) {
			// The HTTP request had an unsuccessful response code.
			return [false, -1];
		} catch (LocalServerException) {
			// The user tried to access `localhost` or similar.
			return [false, -1];
		}

		Filesystem::touch($path);

		$size = filesize($realPath);

		return [true, $size];
	}
}
