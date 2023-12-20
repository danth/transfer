<template>
  <NcModal v-if="visible" @close="close">
    <h2>{{ t('transfer', 'Upload by link') }}</h2>

    <div class="modal-content">
      <NcTextField
        :value.sync="url"
        :label="t('transfer', 'Link')"
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
          class="short"
          :value.sync="chosenExtension"
          :label="t('transfer', 'Extension')"
          :label-visible="true"
          :placeholder="defaults.extension">
        </NcTextField>
      </div>

      <NcNoteCard type="info">
        <p>{{ t('transfer', 'Some websites provide a checksum in addition to the file. This is used after the transfer to verify that the file is not corrupted.') }}</p>
      </NcNoteCard>

      <div class="row">
        <NcSelect
          class="short"
          v-model="hashAlgo"
          inputId="hashAlgo"
          :options="['md5', 'sha1', 'sha256', 'sha512']">
        </NcSelect>

        <NcTextField
          :value.sync="hash"
          :label="t('transfer', 'Checksum')">
        </NcTextField>
      </div>

      <div class="buttons">
        <NcButton
          type="primary"
          nativeType="submit"
          @click="submit"
          :disabled="!isValid">
          <template #icon>
            <NcIconSvgWrapper :svg="TransferSvg" />
          </template>
          {{ t('transfer', 'Upload') }}
        </NcButton>
      </div>
    </div>
  </NcModal>
</template>

<script>
  import { NcButton, NcIconSvgWrapper, NcModal, NcNoteCard, NcSelect, NcTextField } from '@nextcloud/vue'
  import TransferSvg from '@mdi/svg/svg/cloud-upload.svg'
  import pathParse from 'path-parse'
  import { joinPaths } from '@nextcloud/paths'
  import { enqueueTransfer } from './ajax.js'

  export default {
    components: { NcButton, NcIconSvgWrapper, NcModal, NcNoteCard, NcSelect, NcTextField },

    data() {
      return {
        TransferSvg,
        visible: false,
        currentDirectory: null,
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
      open(context) {
        this.visible = true
        this.currentDirectory = context.path
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
h2, .modal-content {
  margin: calc(var(--default-grid-baseline) * 4);
}

.modal-content {
  display: flex;
  flex-direction: column;
  gap: calc(var(--default-grid-baseline) * 4);
}

.row {
  display: flex;
  align-items: baseline;
  gap: calc(var(--default-grid-baseline) * 4);
}

.short {
  width: 12em !important;
}

.notecard {
  margin: 0 !important;
}

.buttons {
  display: flex;
  justify-content: end;
}
</style>
