<template>
  <li>
    <a class="menuitem" @click.stop="open">
      <svg class="svg icon" width="44" height="44" viewBox="0 0 44 44">
        <image
          x="12" y="12" width="20" height="20"
          preserveAspectRatio="xMinYMin meet"
          :xlink:href="icon" />
      </svg>

      <span class="displayname">
        {{ t('transfer', 'Transfer file') }}
      </span>

      <Transfer ref="modal" :currentDirectory="currentDirectory"></Transfer>
    </a>
  </li>
</template>

<script>
  import Vue from 'vue'
  import { generateFilePath } from '@nextcloud/router'
  import Transfer from './Transfer.vue'

  export default {
    components: { Transfer },

    props: ['currentDirectory'],

    computed: {
      icon() {
        return generateFilePath('transfer', 'img', 'app-dark.svg')
      }
    },

    methods: {
      open() {
        window.OC.hideMenus()
        this.$refs.modal.open()
      }
    }
  }
</script>

