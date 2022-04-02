<script>
	import axios from "@nextcloud/axios";
	import { translate as t } from "@nextcloud/l10n";
	import { generateFilePath } from "@nextcloud/router";
	import { onDestroy, onMount } from "svelte";
	const OC = window.OC;

	import { pathStore, reloadHandlerStore, stateStore } from "../store";

	$: loading = false;
	$: url = "";
	$: path = null;
	$: state = null;
	let reloadHandler = () => null;

	let unsubscribeFilename;
	let unsubscribeState;
	let unsubscribeRefreshHandler;
	onMount(() => {
		unsubscribeFilename = pathStore.subscribe(
			value => { path = value; }
		);
		unsubscribeState = stateStore.subscribe(
			value => { state = value; }
		);
		unsubscribeRefreshHandler = reloadHandlerStore.subscribe(
			value => { reloadHandler = value; }
		);
	});
	onDestroy(() => {
		unsubscribeFilename();
		unsubscribeState();
		unsubscribeRefreshHandler();
	});

	function close() {
		stateStore.set(null);
	};

	function submit() {
		stateStore.set("loading");
		axios
			.post(
				generateFilePath("transfer", "ajax", "queue.php"),
				{ path, url }
			)
			.catch((error) => {
				console.error(error);
				stateStore.set("api_error");
			})
			.then((response) => {
				if (response.data.success) {
					stateStore.set("success");
					reloadHandler();
				} else {
					stateStore.set("transfer_error");
				}
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
	{#if state === "success"}
		<div>
			<p>{t(
				"transfer",
				"Transfer complete! {path} is now saved in your Nextcloud.",
				{ path }
			)}</p>
		</div>
		<div class="oc-dialog-buttonrow onebutton">
			<a on:click|preventDefault={close} class="cancel button">
				{t("transfer", "Close")}
			</a>
		</div>
	{/if}
	{#if state === "transfer_error"}
		<div>
			<p>{t("transfer", "There was an error transferring the URL. Perhaps you typed it incorrectly?")}</p>
		</div>
		<div class="oc-dialog-buttonrow onebutton">
			<a on:click|preventDefault={close} class="cancel button">
				{t("transfer", "Close")}
			</a>
		</div>
	{/if}
	{#if state === "api_error"}
		<div>
			<p>{t("transfer", "There was an error sending the URL to Nextcloud.")}</p>
		</div>
		<div class="oc-dialog-buttonrow onebutton">
			<a on:click|preventDefault={close} class="cancel button">
				{t("transfer", "Close")}
			</a>
		</div>
	{/if}
</div>
