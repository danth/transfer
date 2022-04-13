<script>
	import "@nextcloud/dialogs/styles/toast.scss";
	import { joinPaths } from "@nextcloud/paths";
	import { translate as t } from "@nextcloud/l10n";
	const OC = window.OC;

	import { enqueueTransfer } from "../ajax";
	import { makeDefaultFilename } from "../url_utilities";

	export let fileList;
	export let closeHandler;

	$: filename = "";

	// This will be used if the user does not enter their own name.
	$: defaultFilename = t("transfer", "file.txt");

	let url = "";
	// When the URL is edited, update the default filename.
	$: url, setDefaultFilename();

	function setDefaultFilename() {
		// Keeps the current value if the URL is invalid.
		defaultFilename = makeDefaultFilename(url) || defaultFilename;
	}

	function submit() {
		// If the user chose their own filename, use that, otherwise use the default.
		const path = filename
			? joinPaths(fileList.getCurrentDirectory(), filename)
			: joinPaths(fileList.getCurrentDirectory(), defaultFilename);

		enqueueTransfer(path, url);

		closeHandler();
	};
</script>

<div class="oc-dialog-dim" />
<div class="oc-dialog" style="position: fixed;">
	<form
		action={OC.generateUrl('/')}
		on:submit|preventDefault={submit}
		method="post">
		<div>
			<label>
				{t("transfer", "Download link")}
				<br />
				<input
					type="text"
					style="width: 100%; min-width: 25em;"
					bind:value={url}
					autofocus
					placeholder={t("transfer", "http://example.com/file.txt")} />
			</label>
			<label>
				{t("transfer", "File name")}
				<br />
				<input
					type="text"
					style="width: 100%; min-width: 15em;"
					bind:value={filename}
					placeholder={defaultFilename} />
			</label>
		</div>
		<div class="oc-dialog-buttonrow twobuttons">
			<a on:click|preventDefault={closeHandler} class="cancel button">
				{t("transfer", "Cancel")}
			</a>
			<a on:click|preventDefault={submit} class="primary button">
				{t("transfer", "Transfer")}
			</a>
		</div>
	</form>
</div>
