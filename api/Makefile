#!/bin/sh

DOCKER_BE = symfony-api-platform-be
OS := $(shell uname)

ifeq ($(OS),Darwin)
	UID = $(shell id -u)
else ifeq ($(OS),Linux)
	UID = $(shell id -u)
else
	UID = 1000
endif

help:
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\(.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'


run:
	U_ID=${UID} docker-compose up -d 

stop:
	U_ID=${UID} docker-compose stop

restart:
	${MAKE} stop && ${MAKE} run

build:
	U_ID= ${UID} docker composer build

prepare:
	${MAKE} composer-install

composer-install:
	U_ID= ${UID} docker exec --user ${UID} -it ${DOCKER_BE} composer install --no-scripts --no-interaction --optimize-autoloader

migrations:
	U_ID= ${UID} docker exec -it --user ${UID} -${DOCKER_BE} bin/console doctrine:migrations:migrate -n

be-logs:
	U_ID= ${UID} docker exec -it --user ${UID} ${DOCKER_BE} tail -f var/log/dev.log

ssh-be:
	U_ID = ${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash

code-style:
	U_ID = ${UID} docker exec -it --user ${UID} ${DOCKER_BE} php-cs-fixer fix src --rules=@Symfony

generate-ssh-keys: ## Generates SSH keys for the JWT library
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} mkdir -p config/jwt
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} openssl genrsa -passout pass:767b453a97ac019714eb7ccbce781d16 -out config/jwt/private.pem -aes256 4096
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} openssl rsa -pubout -passin pass:767b453a97ac019714eb7ccbce781d16 -in config/jwt/private.pem -out config/jwt/public.pem


