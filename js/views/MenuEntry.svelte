
<script>
	import { translate as t } from "@nextcloud/l10n";
	const OC = window.OC;

  export let openHandler;
  
  function open(event) {
    // Open our dialog.
    openHandler();    

    // Close the "new file" menu.
    OC.hideMenus();

    // Prevent the default action of displaying a text field within the menu.
    event.stopPropagation();
  }
</script>

<li>
  <a class="menuitem" on:click|preventDefault={open}>
    <svg class="svg icon" width="44" height="44" viewBox="0 0 44 44">
      <image
        x="12" y="12" width="20" height="20"
        preserveAspectRatio="xMinYMin meet"
        xlink:href="/apps/transfer/img/app-dark.svg" />
    </svg>
    <span class="displayname">{t("transfer", "Transfer file from URL")}</span>
  </a>
</li>