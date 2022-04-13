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

.PHONY: build
build: build-js

# Builds js/bundle.js
.PHONY: build-js
build-js:
	npm install
	npm run build

# Removes the appstore build
.PHONY: clean
clean:
	rm -rf build js/bundle

# Same as clean, but also removes dependencies installed by npm
.PHONY: distclean
distclean: clean
	rm -rf node_modules

# Builds the appstore package
.PHONY: dist
dist: build appstore

# Builds the source package for the app store
.PHONY: appstore
appstore:
	rm -rf $(appstore_build_directory)
	mkdir -p $(appstore_build_directory)
	tar \
		--transform 's|^\.|$(app_name)|' \
		-cvzf $(appstore_package_name).tar.gz \
		./appinfo \
		./COPYING \
		./img \
		./js/bundle \
		./lib \
		./README.md
