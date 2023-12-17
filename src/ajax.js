import axios from '@nextcloud/axios'
import { showInfo, showError } from '@nextcloud/dialogs'
import { translate as t } from '@nextcloud/l10n'
import { generateFilePath } from '@nextcloud/router'

export function enqueueTransfer(path, url, hashAlgo, hash) {
	axios
		.post(
			generateFilePath('transfer', 'ajax', 'transfer.php'),
			{ path, url, hashAlgo, hash }
		)
		.then((response) => {
			showInfo(t('transfer', 'The upload is queued and will begin processing soon.'))
		})
		.catch((error) => {
			console.error(error)

			if (error.response && error.response.status) {
				showError(t(
					'transfer',
					'Failed to add the upload to the queue. The server responded with status code {statusCode}.',
					{ statusCode: error.response.status }
				))
			} else {
				showError(t('transfer', 'Failed to add the upload to the queue.'))
			}
		})
}
