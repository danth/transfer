<?php
namespace OCA\Transfer\Service;

use OC\Files\Filesystem;

class TransferService {
	private function getSuccess($curl) {
		$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		return (
			$code == 200 ||
			$code == 201 ||
			$code == 203 ||
			($code >= 300 && $code <= 308)
		);
	}

	/**
	 * @param string $url URL of a remote file to determine the size of.
	 * @return Whether the request succeeded, and the size of the file if known.
	 */
	public function getSize(string $url) {
		$curl = curl_init($url);

		// Issue a HEAD request and follow any redirects.
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		curl_exec($curl);

		$code = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
		if ($code == 405) {
			/* 405 means that HEAD requests are not supported by the remote server.
			 * Rather than returning an unsuccessful result, return an unknown size.
			 */
			$success = true;
			$size = -1;
		} else {
			$success = $this->getSuccess($curl);
		}

		$size = curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

		curl_close($curl);

		return [$success, $size];
	}

	/**
	 * @return Whether the transfer succeeded, and the actual size of the
	 * transferred file.
	 */
	public function transfer(string $userId, string $path, string $url) {
		\OC_Util::tearDownFS();
		\OC_Util::setupFS($userId);

		$realPath = Filesystem::getView()->getLocalFile($path);

		$curl = curl_init($url);

		$file = fopen($realPath, "w");
		curl_setopt($curl, CURLOPT_FILE, $file);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

		curl_exec($curl);

		$success = $this->getSuccess($curl);

		fclose($file);
		curl_close($curl);

		$size = -1;
		if ($success) {
			Filesystem::touch($path);
			$size = filesize($realPath);
		}

		return [$success, $size];
	}
}
