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
			showError(error);
		});
}
