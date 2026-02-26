.PHONY: sh-app sh-nginx sh-postgres sh fix-permissions up logs

# Corrige permissões de storage e bootstrap/cache no container da aplicação
fix-permissions:
	docker compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

sh-app:
	docker compose exec app sh

sh-nginx:
	docker compose exec nginx sh

sh-postgres:
	docker compose exec postgres sh

up:
	docker compose up -d

down:
	docker compose down

logs:
	docker compose logs -f app-dev

