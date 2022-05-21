import axios from "@nextcloud/axios";
import { showInfo, showError } from '@nextcloud/dialogs';
import { translate as t } from "@nextcloud/l10n";
import { generateFilePath } from "@nextcloud/router";

export function enqueueTransfer(path, url) {
	axios
		.post(
			generateFilePath("transfer", "ajax", "transfer.php"),
			{ path, url }
		)
		.then((response) => {
			showInfo(t("transfer", "Transfer queued to run in the background."));
		})
		.catch((error) => {
			console.error(error);

			if (error.response && error.response.status) {
				showError(t(
					"transfer",
					"Could not queue the transfer. The server responded with status code {statusCode}.",
					{ statusCode: error.response.status }
				));
			} else {
				showError(t("transfer", "Could not queue the transfer."));
			}
		});
}
