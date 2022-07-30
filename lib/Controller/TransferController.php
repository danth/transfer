<?php
namespace OCA\Transfer\Controller;

use OCP\BackgroundJob\IJobList;
use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCA\Transfer\BackgroundJob\TransferJob;
use OCA\Transfer\Service\TransferService;

class TransferController extends Controller {
	private $userId;
	private $jobList;

	public function __construct(
		$AppName,
		IRequest $request,
		IJobList $jobList,
		TransferService $service,
		$UserId
	) {
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->jobList = $jobList;
		$this->service = $service;
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
	public function transfer(string $path, string $url, string $hashAlgo, string $hash) {
		$this->jobList->add(TransferJob::class, [
			"userId" => $this->userId,
			"path" => $path,
			"url" => $url,
			"hashAlgo" => $hashAlgo,
			"hash" => $hash,
		]);

		return new DataResponse(true, Http::STATUS_OK);
	}
}
