# This file is licensed under the Affero General Public License version 3 or
# later. See the COPYING file.
# @author Bernhard Posselt <dev@bernhard-posselt.com>
# @copyright Bernhard Posselt 2016

app_name=transfer
build_tools_directory=$(CURDIR)/build/tools
source_build_directory=$(CURDIR)/build/artifacts/source
source_package_name=$(source_build_directory)/$(app_name)
appstore_build_directory=$(CURDIR)/build/artifacts/appstore
appstore_package_name=$(appstore_build_directory)/$(app_name)
npm=$(shell which npm 2> /dev/null)

all: build

.PHONY: build
build:
	make npm

# Builds js/bundle.js
.PHONY: npm
npm:
	npm install
	npm run build

# Removes the appstore build
.PHONY: clean
clean:
	rm -rf ./build

# Same as clean, but also removes dependencies installed by npm
.PHONY: distclean
distclean: clean
	rm -rf node_modules

# Builds the source and appstore packages
.PHONY: dist
dist:
	make build
	make source
	make appstore

# Builds the source package
.PHONY: source
source:
	rm -rf $(source_build_directory)
	mkdir -p $(source_build_directory)
	tar \
		--exclude-vcs \
		--exclude="../$(app_name)/build" \
		--exclude="../$(app_name)/js/node_modules" \
		--exclude="../$(app_name)/node_modules" \
		--exclude="../$(app_name)/*.log" \
		--exclude="../$(app_name)/js/*.log" \
		-cvzf $(source_package_name).tar.gz ../$(app_name)

# Builds the source package for the app store
.PHONY: appstore
appstore:
	rm -rf $(appstore_build_directory)
	mkdir -p $(appstore_build_directory)
	tar \
		--exclude-vcs \
		--exclude="../$(app_name)/build" \
		--exclude="../$(app_name)/tests" \
		--exclude="../$(app_name)/Makefile" \
		--exclude="../$(app_name)/*.log" \
		--exclude="../$(app_name)/phpunit*xml" \
		--exclude="../$(app_name)/composer.*" \
		--exclude="../$(app_name)/js/node_modules" \
		--exclude="../$(app_name)/js/tests" \
		--exclude="../$(app_name)/js/test" \
		--exclude="../$(app_name)/js/*.log" \
		--exclude="../$(app_name)/js/package.json" \
		--exclude="../$(app_name)/js/bower.json" \
		--exclude="../$(app_name)/js/karma.*" \
		--exclude="../$(app_name)/js/protractor.*" \
		--exclude="../$(app_name)/package.json" \
		--exclude="../$(app_name)/bower.json" \
		--exclude="../$(app_name)/karma.*" \
		--exclude="../$(app_name)/protractor\.*" \
		--exclude="../$(app_name)/.*" \
		--exclude="../$(app_name)/js/.*" \
		-cvzf $(appstore_package_name).tar.gz ../$(app_name)
