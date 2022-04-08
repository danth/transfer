
<script>
	import { translate as t } from "@nextcloud/l10n";
	const OC = window.OC;

	import { visibleStore } from "../store";
  
  function open(event) {
    // Open our dialog.
    visibleStore.set(true);
    
    // Close the "new file" menu.
    OC.hideMenus();
    
    // Prevent the default action of displaying a text field within the menu.
    event.stopPropagation();
  }
</script>

<li>
  <a class="menuitem" on:click|preventDefault={open}>
    <span class="icon icon-upload svg" />
    <span class="displayname">{t("transfer", "Transfer file from URL")}</span>
  </a>
</li>