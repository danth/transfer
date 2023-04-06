<template>
  <NcModal v-if="visible" @close="close" title="Transfer file">
    <div class="modal-content">
      <NcTextField
        :value.sync="url"
        :label="t('transfer', 'Download link')"
        :label-visible="true"
        placeholder="https://example.com/file.txt">
      </NcTextField>

			<div class="row">
        <NcTextField
          :value.sync="chosenName"
          :label="t('transfer', 'File name')"
          :label-visible="true"
          :placeholder="defaults.name">
        </NcTextField>
        .
        <NcTextField
          style="width: 8em"
          :value.sync="chosenExtension"
          :label="t('transfer', 'Extension')"
          :label-visible="true"
          :placeholder="defaults.extension">
        </NcTextField>
      </div>

      <p>{{ t('transfer', 'If you have a checksum, enter it below.') }}</p>

      <NcSelect
        v-model="hashAlgo"
        inputId="hashAlgo"
        :options="['md5', 'sha1', 'sha256', 'sha512']">
      </NcSelect>

      <NcTextField
        v-if="hashAlgo"
        :value.sync="hash"
        :label="t('transfer', 'Hash')"
        placeholder="64ec88ca00b268e5ba1a35678a1b5316d212f4f366b2477232534a8aeca37f3c"
				style="flex-grow: 1;">
      </NcTextField>

  		<div class="oc-dialog-buttonrow twobuttons">
        <NcButton
          type="secondary"
          @click="close">
          {{ t('transfer', 'Cancel') }}
        </NcButton>

        <NcButton
          type="primary"
          nativeType="submit"
          @click="submit"
          :disabled="!isValid">
          {{ t('transfer', 'Transfer') }}
        </NcButton>
      </div>
    </div>
  </NcModal>
</template>

<script>
  import NcButton from '@nextcloud/vue/dist/Components/NcButton'
  import NcModal from '@nextcloud/vue/dist/Components/NcModal'
  import NcSelect from '@nextcloud/vue/dist/Components/NcSelect'
  import NcTextField from '@nextcloud/vue/dist/Components/NcTextField'
  import pathParse from 'path-parse'
  import { joinPaths } from '@nextcloud/paths'
  import { enqueueTransfer } from './ajax.js'

  export default {
    components: { NcButton, NcModal, NcSelect, NcTextField },

    props: ['currentDirectory'],

    data() {
      return {
        visible: false,
        url: '',
        chosenName: '',
        chosenExtension: '',
        hashAlgo: null,
        hash: ''
      }
    },

    computed: {
      defaults() {
        try {
          const url = new URL(this.url)
          const path = pathParse(url.pathname)

          return {
            name: path.name,
            extension: path.ext.substring(1)
          }
        } catch (TypeError) {
          // The current URL is not valid
          return { name: '', extension: '' }
        }
      },

      name() {
        return this.chosenName || this.defaults.name
      },

      extension() {
        return this.chosenExtension || this.defaults.extension
      },

      isValid() {
        try {
          const url = new URL(this.url)
        } catch (TypeError) {
          // The current URL is not valid
          return false
        }

        return this.name && this.extension
      }
    },

    methods: {
      open() {
        this.visible = true
      },

      close() {
        this.visible = false
      },

      submit() {
        const fullName = `${this.name}.${this.extension}`
        const path = joinPaths(this.currentDirectory, fullName)
        enqueueTransfer(path, this.url, this.hashAlgo || '', this.hash)
        this.close()
      }
    }
  }
</script>

<style scoped>
.modal-content {
	margin: 50px;

  display: flex;
  flex-direction: column;
  gap: 10px;
}

.modal-content > p {
	text-align: center;
}

.row {
  display: flex;
  gap: 10px;
  align-items: last baseline;
}
</style>
