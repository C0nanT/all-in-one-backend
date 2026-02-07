.PHONY: sh-app sh-nginx sh-postgres sh fix-permissions

# Corrige permissões de storage e bootstrap/cache no container da aplicação
fix-permissions:
	docker compose exec application chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Acessa o shell do container da aplicação (PHP)
sh-app:
	docker compose exec application sh

# Acessa o shell do container Nginx
sh-nginx:
	docker compose exec nginx sh

# Acessa o shell do container PostgreSQL
sh-postgres:
	docker compose exec postgres sh

# Atalho: sh sem argumento abre o container da aplicação
sh: sh-app
