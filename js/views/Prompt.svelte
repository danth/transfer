<script>
	import axios from "@nextcloud/axios";
	import { showInfo, showError } from '@nextcloud/dialogs';
	import "@nextcloud/dialogs/styles/toast.scss";
	import { joinPaths } from "@nextcloud/paths"; 
	import { generateFilePath } from "@nextcloud/router";
	import { translate as t } from "@nextcloud/l10n";
	import { get } from 'svelte/store'
	import { onDestroy, onMount } from "svelte";
	const OC = window.OC;

	import { directoryStore, visibleStore } from "../store";

	$: visible = false;
	let unsubscribeVisible;
	onMount(() => {
		unsubscribeVisible = visibleStore.subscribe(
			value => { visible = value; }
		);
	});
	onDestroy(() => {
		unsubscribeVisible();
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
		visibleStore.set(false);
	};
	
	function submit() {
		visibleStore.set(false);
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
			.then((response) => {
				showInfo(t("transfer", "Transfer queued to run in the background."));
			})
			.catch((error) => {
				console.error(error);
				showError(error);
			});
	};
</script>

{#if visible}
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
			<a on:click|preventDefault={close} class="cancel button">
				{t("transfer", "Cancel")}
			</a>
			<a on:click|preventDefault={submit} class="primary button">
				{t("transfer", "Transfer")}
			</a>
		</div>
	</form>
</div>
{/if}
