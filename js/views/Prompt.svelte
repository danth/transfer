<script>
	import "@nextcloud/dialogs/styles/toast.scss";
	import { joinPaths } from "@nextcloud/paths";
	import { translate as t } from "@nextcloud/l10n";
	const OC = window.OC;

	import { enqueueTransfer } from "../ajax";
	import { makeDefaultFilename, makeDefaultExtension } from "../url_utilities";

	export let fileList;
	export let closeHandler;

	$: filename = "";
	$: extension = "";
	$: hashAlgo = "";
	$: hash = "";

	// This will be used if the user does not enter their own name.
	$: defaultFilename = t("transfer", "file");
	$: defaultExtension = "";

	let url = "";
	// When the URL is edited, update the default filename.
	$: url, setDefaultFilename();

	function setDefaultFilename() {
		// Keeps the current value if the URL is invalid.
		defaultFilename = makeDefaultFilename(url) || defaultFilename;
		defaultExtension = makeDefaultExtension(url) || defaultExtension;
	}

	function submit() {
		const fullFilename = `${filename || defaultFilename}.${extension || defaultExtension}`;
		const path = joinPaths(fileList.getCurrentDirectory(), fullFilename);

		enqueueTransfer(path, url, hashAlgo, hash);

		closeHandler();
	};
</script>

<div class="oc-dialog-dim" />
<div class="oc-dialog" style="position: fixed;">
	<form
		action={OC.generateUrl('/')}
		on:submit|preventDefault={submit}
		method="post">
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
		<div style="margin-top: 0.5em;">
			<div style="display: flex; align-items: last baseline; gap: 0.15em;">
				<label style="flex-grow: 1;">
					{t("transfer", "File name")}
					<br />
					<input
						type="text"
						style="width: 100%;"
						bind:value={filename}
						placeholder={defaultFilename} />
				</label>
				.
				<label>
					{t("transfer", "Extension")}
					<br />
					<input
						type="text"
						style="width: 5em;"
						bind:value={extension}
						placeholder={defaultExtension} />
				</label>
			</div>
			<p style="text-align: center; margin-top: 0.4em;">
				{t("transfer", "The file name and extension will be detected automatically, if left blank.")}
			</p>
		</div>
		<div style="margin-top: 0.5em;">
			<div style="display: flex; align-items: last baseline; gap: 0.15em;">
				<label>
					{t("transfer", "Hash type")}
					<br />
					<select bind:value={hashAlgo}>
						<option value="md5">md5</option>
						<option value="sha1">sha1</option>
						<option value="sha256">sha256</option>
						<option value="sha512">sha512</option>
					</select>
				</label>
				<label style="flex-grow: 1;">
					{t("transfer", "Hash")}
					<br />
					<input
						type="text"
						style="width: 100%;"
						bind:value={hash}
						placeholder="64ec88ca00b268e5ba1a35678a1b5316d212f4f366b2477232534a8aeca37f3c" />
				</label>
			</div>
			<p style="text-align: center; margin-top: 0.4em;">
				{t("transfer", "Enter the checksum above, if you have one.")}
			</p>
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
