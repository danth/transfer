<template>
  <li>
    <a class="menuitem" @click.stop="open">
      <svg class="svg icon" viewBox="0 0 10 10">
        <path fill="currentColor" d="M 2.6359711,1.5625117 0.89397505,3.3045077 2.6359711,5.0465033 V 4.0018944 H 8.0476922 V 3.3045077 2.6071206 H 2.6359711 Z" />
        <path fill="currentColor" d="M 7.3640303,4.9534972 9.106025,6.6954927 7.3640303,8.4374883 V 7.3928799 H 1.9523082 V 6.6954927 5.9981059 h 5.4117221 z" />
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
  import Transfer from './Transfer.vue'

  export default {
    components: { Transfer },

    props: ['currentDirectory'],

    methods: {
      open() {
        window.OC.hideMenus()
        this.$refs.modal.open()
      }
    }
  }
</script>

<style scoped>
svg {
  width: 16px;
  height: 16px;
  margin: 14px;
}
</style>
