SHELL := /bin/bash
# Note: this needs tabs, not spaces

.PHONY:

all: init

install:
	@yarn install
	@bundle install
	@composer install

assets:
	@yarn run encore dev

watch:
	@yarn run encore dev --watch

init: install assets

demo: install assets

dev-server:
	@docker-compose -f docker-compose.yml -f docker-compose.dev.yml up

dev-server-build:
	@docker-compose -f docker-compose.yml -f docker-compose.dev.yml build

dev-sh:
	@docker exec -it defi_php su -s /bin/bash www-data

dev-sh-root:
	@docker exec -it defi_php /bin/bash
