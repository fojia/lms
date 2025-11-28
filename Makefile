build:
	@echo 'Building Docker image...'
	docker compose build

up:
	@echo 'Starting containers...'
	docker compose up -d

down:
	@echo 'Stopping containers...'
	docker compose down

shell:
	@echo 'Opening shell...'
	docker compose exec app sh

install:
	@echo 'Installing dependencies...'
	docker compose exec app composer install

update:
	@echo 'Updating dependencies...'
	docker compose exec app composer update

test:
	@echo 'Running all tests...'
	docker compose exec app vendor/bin/phpunit --testdox

autoload:
	@echo 'Regenerating autoload...'
	docker compose exec app composer dump-autoload
