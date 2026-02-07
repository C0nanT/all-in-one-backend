.PHONY: sh-app sh-nginx sh-postgres sh

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
