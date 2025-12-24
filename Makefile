.DEFAULT_GOAL := setup

.PHONY: setup
setup: ## Setup the project
	@composer install
	@cp .env.example .env
	@php artisan key:generate
	@php artisan migrate
	@npm install --legacy-peer-deps

dev: ## Run dev server
	@npm run dev