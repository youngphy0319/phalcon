list:
	@echo "update"
	@echo "upgrade"
	@echo "rollback"
	@echo "clear-all"
	@echo "sendmail"
	@echo "test"
	@echo "git-pull"
	@echo "git-push"
	@echo "install-dev"
	@echo "install"


test:
	phpunit --bootstrap Bootstrap.php ./tests

git-pull:
	git pull origin master

git-push:
	git push -u origin master

rollback:
	git reset --hard HEAD~1

clear-all:
	rm config/_*
	rm cache/api/*
	rm cache/global/*
	rm cache/html/*
	rm cache/model/*
	rm cache/schema/*
	rm cache/view/*

sendmail:
	php workers/sendmail.php

upgrade:
	git pull origin master
	composer update
	git submodule update --init --recursive

update:
	git pull origin master
	git submodule update --init --recursive

install-dev:
	composer install --dev
	git submodule update --init --recursive
	sudo chown -R www-data.www-data ./config
	sudo chown -R www-data.www-data ./logs
	sudo chown -R www-data.www-data ./cache
	sudo chown -R www-data.www-data ./public/uploads
	sudo chown -R www-data.www-data ./public/cache
	sudo chown -R www-data.www-data ./public/tmp
	sudo chown -R www-data.www-data ./public/thumbnails/thumb

install:
	composer install --no-dev --optimize-autoloader
	git submodule update --init --recursive
	sudo chown -R www-data.www-data ./config
	sudo chown -R www-data.www-data ./logs
	sudo chown -R www-data.www-data ./cache
	sudo chown -R www-data.www-data ./public/uploads
	sudo chown -R www-data.www-data ./public/cache
	sudo chown -R www-data.www-data ./public/tmp
	sudo chown -R www-data.www-data ./public/thumbnails/thumb