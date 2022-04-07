<script>
	import axios from "@nextcloud/axios";
	import { generateFilePath } from "@nextcloud/router";
	import { translate as t } from "@nextcloud/l10n";
	import { onDestroy, onMount } from "svelte";
	const OC = window.OC;

	import { pathStore, stateStore } from "../store";

	$: loading = false;
	$: url = "";
	$: path = null;
	$: state = null;
	let reloadHandler = () => null;

	let unsubscribeFilename;
	let unsubscribeState;
	onMount(() => {
		unsubscribeFilename = pathStore.subscribe(
			value => { path = value; }
		);
		unsubscribeState = stateStore.subscribe(
			value => { state = value; }
		);
	});
	onDestroy(() => {
		unsubscribeFilename();
		unsubscribeState();
	});

	function close() {
		stateStore.set(null);
	};

	function submit() {
		stateStore.set("loading");
		axios
			.post(
				generateFilePath("transfer", "ajax", "transfer.php"),
				{ path, url }
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
					{t("transfer", "Enter download link for {path}", { path })}
					<br />
					<input
						type="text"
						style="width: 100%;"
						class="input-wide"
						bind:value={url}
						autofocus
						placeholder={t("transfer", "http://example.com/file.txt")} />
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
			<p>{t("transfer", "{path} will appear in your files when the job is finished.", { path })}</p>
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
