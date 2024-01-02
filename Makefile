.PHONY: test

CONTAINER_PHP = graylog-php-test

test:
	clear && \
	docker exec ${CONTAINER_PHP} php ./test/test.php

install:
	clear && \
	docker compose up -d && \
	docker exec -it ${CONTAINER_PHP} sh -c "cd /var/www/html && composer update"

up:
	docker compose up -d

down:
	docker compose down

autoload:
	clear && \
	sudo rm -rf vendor/ && \
	docker exec -it ${CONTAINER_PHP} sh -c "cd /var/www/html && composer dump-autoload"