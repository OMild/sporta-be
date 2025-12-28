.PHONY: help build up down restart logs shell artisan composer npm test fresh

# Colors
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

help: ## Show this help
	@echo ''
	@echo 'Usage:'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<target>${RESET}'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-15s${RESET} %s\n", $$1, $$2}' $(MAKEFILE_LIST)

build: ## Build Docker images
	docker compose build --no-cache

up: ## Start all containers
	docker compose up -d

down: ## Stop all containers
	docker compose down

restart: ## Restart all containers
	docker compose restart

logs: ## View container logs
	docker compose logs -f

shell: ## Access app container shell
	docker compose exec app bash

artisan: ## Run artisan command (usage: make artisan cmd="migrate")
	docker compose exec app php artisan $(cmd)

composer: ## Run composer command (usage: make composer cmd="require package")
	docker compose exec app composer $(cmd)

npm: ## Run npm command (usage: make npm cmd="install")
	docker compose exec app npm $(cmd)

test: ## Run PHPUnit tests
	docker compose exec app php artisan test

fresh: ## Fresh install (rebuild + migrate fresh)
	docker compose down -v
	docker compose build --no-cache
	docker compose up -d

mysql: ## Access MySQL CLI
	docker compose exec mysql mysql -u sporta -psecret sporta

redis: ## Access Redis CLI
	docker compose exec redis redis-cli
