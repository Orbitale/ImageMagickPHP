SHELL := /bin/bash

IMAGEMAGICK_DOCKER_IMAGE = orbitale-imphp
PHP_BIN = php

# Helper vars
_TITLE := "\033[32m[%s]\033[0m %s\n"
_ERROR := "\033[31m[%s]\033[0m %s\n"

.DEFAULT_GOAL := help
help: ## Show this help.
	@printf "\n Available commands:\n\n"
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-25s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## */[33m/'
.PHONY: help

install: vendor imagemagick-docker ## Install composer dependencies and Docker image for testing
.PHONY: install

start: ## Start Docker image for testing
	@docker start $(IMAGEMAGICK_DOCKER_IMAGE)
.PHONY: start

stop: ## Stop testing Docker image
	@docker stop $(IMAGEMAGICK_DOCKER_IMAGE)
.PHONY: stop

test: start ## Start Docker image for testing
	export IMAGEMAGICK_PATH="docker exec $(IMAGEMAGICK_DOCKER_IMAGE) `pwd`/docker_entrypoint.sh magick" && \
	$(PHP_BIN) vendor/bin/phpunit
	$(MAKE) stop
.PHONY: test

vendor:
	@printf $(_TITLE) "Install" "Installing Composer dependencies"
	@composer update
.PHONY: vendor

imagemagick-docker:
	@printf $(_TITLE) "Install" "Removing existing Docker image"
	@docker rm --force --volumes $(IMAGEMAGICK_DOCKER_IMAGE)
	@printf $(_TITLE) "Install" "Creating ImageMagick Docker image for development"
	@docker create \
		--name=$(IMAGEMAGICK_DOCKER_IMAGE) \
		--volume `pwd`:`pwd` \
		--workdir=`pwd` \
		--entrypoint="`pwd`/docker_entrypoint.sh" \
		dpokidov/imagemagick:latest \
		sleep 9999999 \
		>/dev/null
.PHONY: imagemagick-docker