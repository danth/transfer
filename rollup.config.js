import commonjs from "@rollup/plugin-commonjs";
import nodePolyfills from "rollup-plugin-polyfill-node";
import resolve from "@rollup/plugin-node-resolve";
import svelte from "rollup-plugin-svelte";

export default {
	input: "js/index.js",
	output: {
		file: "js/bundle.js",
		format: "iife",
	},
	plugins: [
		commonjs(),
		nodePolyfills(),
		resolve(),
		svelte(),
	],
};
