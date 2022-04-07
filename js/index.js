import { joinPaths } from "@nextcloud/paths";
import { translate as t } from "@nextcloud/l10n";

import App from "./views/App.svelte";
import { pathStore, stateStore } from "./store";

new App({
	target: document.body,
	props: {},
})

window.OC.Plugins.register("OCA.Files.NewFileMenu", {
	attach: (menu) => {
		const fileList = menu.fileList;

		// Only show in the main file list
		if (fileList.id !== "files") return;

		menu.addMenuEntry({
			id: "transfer",
			displayName: t("transfer", "Transfer file from URL"),
			iconClass: "icon-upload",
			fileType: "file",
			actionHandler: (filename) => {
				const directory = fileList.getCurrentDirectory();
				pathStore.set(joinPaths(directory, filename));

				stateStore.set("input");
			},
		});
	},
});
