const webpackConfig = require('@nextcloud/webpack-vue-config')
const webpackRules = require('@nextcloud/webpack-vue-config/rules')

// Custom rule for SVGs
webpackRules.RULE_SVGS = {
	test: /\.svg$/,
	type: 'asset/source'
}

// Remove SVG from the assets rule
webpackRules.RULE_ASSETS.test = /\.(png|jpe?g|gif|woff2?|eot|ttf)$/

webpackConfig.module.rules = Object.values(webpackRules)

module.exports = webpackConfig
