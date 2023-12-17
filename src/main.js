import Vue from 'vue'
import { addNewFileMenuEntry, Permission } from '@nextcloud/files'
import { translate, translatePlural } from '@nextcloud/l10n'
import Transfer from './Transfer.vue'
import TransferSvg from '@mdi/svg/svg/cloud-upload.svg'

Vue.prototype.t = translate
Vue.prototype.n = translatePlural

const vueMountElement = document.createElement('div')
document.body.append(vueMountElement)

const vueMount = new Vue({
	el: vueMountElement,
	render: h => h(Transfer)
})

addNewFileMenuEntry({
	id: 'transfer',
	displayName: t('transfer', 'Upload by link'),
	iconSvgInline: TransferSvg,
	order: -1,

	// Only display in folders where this user has permission to create files
	if: context => (context.permissions & Permission.CREATE) !== 0,

	async handler(context, content) {
		vueMount.$children[0].open(context)
	}
})
