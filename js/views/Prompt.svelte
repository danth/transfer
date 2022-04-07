<script>
	import axios from "@nextcloud/axios";
	import { joinPaths } from "@nextcloud/paths"; 
	import { generateFilePath } from "@nextcloud/router";
	import { translate as t } from "@nextcloud/l10n";
	import { get } from 'svelte/store'
	import { onDestroy, onMount } from "svelte";
	const OC = window.OC;

	import { directoryStore, stateStore } from "../store";

	// The current state of the dialog.
	$: state = null;
	let unsubscribeState;
	onMount(() => {
		unsubscribeState = stateStore.subscribe(
			value => { state = value; }
		);
	});
	onDestroy(() => {
		unsubscribeState();
	});

	$: filename = "";

	// This will be used if the user does not enter their own name.
	$: defaultFilename = t("transfer", "file.txt");

	let url = "";
	// When the URL is edited, update the default filename.
	$: url, setDefaultFilename();

	function setDefaultFilename() {
		let segments;
		try {
			segments = new URL(url).pathname.split('/');
		} catch (TypeError) {
			// Do nothing if the URL fails to parse.
			return;
		}

		// The || handles the possibility of a trailing slash.
		defaultFilename = segments.pop() || segments.pop();
	}

	function close() {
		stateStore.set(null);
	};
	
	function submit() {
		stateStore.set("loading");
		axios
			.post(
				generateFilePath("transfer", "ajax", "transfer.php"),
				{
					/* If the user chose their own filename, use that,
					 * otherwise use the default.
					 */
					path: filename
						? joinPaths(get(directoryStore), filename)
						: joinPaths(get(directoryStore), defaultFilename),
					url
				}
			)
			.catch((error) => {
				console.error(error);
				stateStore.set("api_error");
			})
			.then((response) => {
				stateStore.set("queued");
			});
	};
</script>

<div class="oc-dialog-dim" />
<div
	class={`oc-dialog ${state === "loading" ? "icon-loading" : ""}`}
	style="position: fixed;">
	{#if state === "input"}
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
						autofocus
						placeholder={defaultFilename} />
				</label>
			</div>
			<div class="oc-dialog-buttonrow twobuttons">
				<a on:click|preventDefault={close} class="cancel button">
					{t("transfer", "Cancel")}
				</a>
				<a on:click|preventDefault={submit} class="primary button">
					{t("transfer", "Transfer")}
				</a>
			</div>
		</form>
	{/if}
	{#if state === "queued"}
		<div>
			<p>{t("transfer", "The download has been queued to run in the background.")}</p>
			<p>{t("transfer", "{filename} will appear in your files when the job is finished.", { filename })}</p>
		</div>
		<div class="oc-dialog-buttonrow onebutton">
			<a on:click|preventDefault={close} class="cancel button">
				{t("transfer", "Close")}
			</a>
		</div>
	{/if}
	{#if state === "api_error"}
		<div>
			<p>{t("transfer", "There was an error during submission of the URL to Nextcloud.")}</p>
		</div>
		<div class="oc-dialog-buttonrow onebutton">
			<a on:click|preventDefault={close} class="cancel button">
				{t("transfer", "Close")}
			</a>
		</div>
	{/if}
</div>
