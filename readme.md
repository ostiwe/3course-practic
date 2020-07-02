## Docker setup

1) Rename .env to .env.def
2) Rename .env.docker to .env
3) Run `docker-compose build`
4) Run `docker-compose run`
5) Connect to `api_app_phpfpm` container and run in console `php bin/console app:init`