.PHONY: npm-install
npm-install:
	npm install

.PHONY: npm-build
npm-build: npm-install
	npm run build

.PHONY: icon
icon: npm-install
	sed 's/path/path fill="#fff"/g' \
		<node_modules/@mdi/svg/svg/cloud-upload.svg \
		>img/app.svg
	sed 's/path/path fill="#000"/g' \
		<node_modules/@mdi/svg/svg/cloud-upload.svg \
		>img/app-dark.svg

.PHONY: build
build: npm-build icon

.PHONY: dist
dist: build
	rm -f transfer.tar.gz
	tar \
		--transform 's|^\.|transfer|' \
		-cvzf transfer.tar.gz \
		./appinfo \
		./COPYING \
		./img \
		./js \
		./lib \
		./l10n \
		./README.md
