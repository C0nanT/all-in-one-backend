.PHONY: sh-app sh-nginx sh-postgres sh fix-permissions up logs

# Corrige permissões de storage e bootstrap/cache (editável no host; aplicado também no entrypoint ao subir)
fix-permissions:
	docker compose exec app sh -c 'chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && chmod -R 777 /var/www/storage /var/www/bootstrap/cache'

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
	docker logs -f ai1-app

