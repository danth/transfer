<script>
	import { onDestroy, onMount } from "svelte";

	import Prompt from "./Prompt.svelte";
	import { stateStore } from "../store";

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
</script>

{#if state}
	<Prompt />
{/if}
