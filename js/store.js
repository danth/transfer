import { writable } from "svelte/store";

export const pathStore = writable(null);
export const stateStore = writable(null);
export const reloadHandlerStore = writable(() => null);
