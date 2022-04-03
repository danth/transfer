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
	public function transfer(string $path, string $url) {
		[$success, $size] = $this->service->getSize($url);

		if (!$success) {
			// The HTTP response code when we asked for the size indicated failure.

			return new DataResponse([
				"status" => "failed",
				"size" => $size,
			], Http::STATUS_OK);
		}

		if ($size > 26214400) {
			// Queue transfers larger than 25MiB to run in the background.

			$this->jobList->add(TransferJob::class, [
				"userId" => $this->userId,
				"path" => $path,
				"url" => $url,
			]);

			return new DataResponse([
				"status" => "queued",
				"size" => $size,
			], Http::STATUS_OK);

		} else {
			/* Run smaller transfers immediately.
			 *
			 * This will also happen for unknown sizes, which usually occur because
			 * the remote server is generating a webpage on-the-fly.
			 *
			 * The actual size of the downloaded file is measured so that we can
			 * provide a value to the client even if the size was unknown before the
			 * transfer.
			 */

			[$success, $size] = $this->service->transfer($this->userId, $path, $url);

			return new DataResponse([
				"status" => $success ? "done" : "failed",
				"size" => $size,
			], Http::STATUS_OK);
		}
	}
}
