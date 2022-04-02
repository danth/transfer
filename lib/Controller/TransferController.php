<?php
namespace OCA\Transfer\Controller;

use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OC\Files\Filesystem;

class TransferController extends Controller {
	private $userId;

	public function __construct($AppName, IRequest $request, $UserId) {
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
	}

	
	private function getRealPath(string $path) {
		\OC_Util::tearDownFS();
		\OC_Util::setupFS($this->userId);
		return Filesystem::getView()->getLocalFile($path);
	}

	/**
	 * CAUTION: the @Stuff turns off security checks; for this page no admin is
	 *          required and no CSRF check. If you don't know what CSRF is, read
	 *          it up in the docs or you might create a security hole. This is
	 *          basically the only required method to add this exemption, don't
	 *          add it to any other method if you don't exactly know what it does
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function queue(string $path, string $url) {
		$realPath = $this->getRealPath($path);
		exec(
			"curl --fail " . escapeshellarg($url) . " -o " . escapeshellarg($realPath),
			$output, $exitCode
		);

		if ($exitCode == 0) {
			Filesystem::touch($path);
			return new DataResponse(array("success" => true), Http::STATUS_OK);
		} else {
			return new DataResponse(array("success" => false), Http::STATUS_OK);
		}
	}

}
