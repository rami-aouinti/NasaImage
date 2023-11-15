# PHP symfony 
Docker environment (based on official php docker hub repositories) required to run Symfony 6.

[Source code](https://github.com/rami-aouinti/NasaImage.git)

## Requirements
* Docker version 18.06 or later
* Docker compose version 1.22 or later
* An editor or IDE

Note: OS recommendation - Linux Ubuntu based.

## Components
1. Nginx 1.25
2. PHP 8.1 fpm
4. Symfony 6

## Setting up Docker and docker-compose
1.For installing Docker please follow steps mentioned on page [install on Ubuntu linux](https://docs.docker.com/install/linux/docker-ce/ubuntu/) or [install on Mac/Windows](https://docs.docker.com/engine/install/).

2.For installing docker-compose as `Linux Standalone binary` please follow steps on the page [install compose](https://docs.docker.com/compose/install/standalone/) if you are using Linux OS.

Note 1: Please run next cmd after above step 2 if you are using Linux OS: `sudo usermod -aG docker $USER`

Note 2: If you are using Docker Desktop for MacOS 12.2 or later - please enable [virtiofs](https://www.docker.com/blog/speed-boost-achievement-unlocked-on-docker-desktop-4-6-for-mac/) for performance (enabled by default since Docker Desktop v4.22).

## Setting up DEV environment
1.You can clone this repository from GitHub or install via composer.

If you have installed composer and want to install environment via composer you can use next cmd command:
```bash
composer create-project nasa-images/docker-symfony-api api-example-app
```

2.Set another APP_SECRET for application in .env.prod and .env.staging files if exist.

Note 1: You can get unique secret key for example [here](http://nux.net/secret).

Note 2: Do not use .env.local.php on dev and test environment (delete it if it exist).

Note 3: If you want to change default web port/xdebug configuration you can create .env.local file and set some params (see .env file).


3.Add domain to local 'hosts' file:
```bash
127.0.0.1    localhost
```

4.Configure `/docker/dev/xdebug-main.ini` (Linux/Windows) or `/docker/dev/xdebug-osx.ini` (MacOS) (optional):

- In case you need debug only requests with IDE KEY: PHPSTORM from frontend in your browser:
```bash
xdebug.start_with_request = no
```
Install locally in Firefox extension "Xdebug helper" and set in settings IDE KEY: PHPSTORM

- In case you need debug any request to an api (by default):
```bash
xdebug.start_with_request = yes
```

Note: For prod/staging environment another password should be used.

6.Build, start and install the docker images from your terminal:
```bash
make build
make start
make composer-install
```

## Getting shell to container
After application will start (`make start`) and in order to get shell access inside symfony container you can run following command:
```bash
make ssh
```
Note 1: Please use next make commands in order to enter in other containers: `make ssh-nginx`, `make ssh-supervisord`, `make ssh-mysql`, `make ssh-rabbitmq`.

Note 2: Please use `exit` command in order to return from container's shell to local shell.

## Building containers
In case you edited Dockerfile or other environment configuration you'll need to build containers again using next commands:
```bash
make down
make build
make start
```
Note: Please use environment-specific commands if you need to build test/staging/prod environment, more details can be found using help `make help`.

## Start and stop environment containers
Please use next make commands in order to start and stop environment:
```bash
make start
make stop
```
Note 1: For staging environment need to be used next make commands: `make start-staging`, `make stop-staging`.

Note 2: For prod environment need to be used next make commands: `make start-prod`, `make stop-prod`.

## Stop and remove environment containers, networks
Please use next make commands in order to stop and remove environment containers, networks:
```bash
make down
```
Note: Please use environment-specific commands if you need to stop and remove test/staging/prod environment, more details can be found using help `make help`.

## Additional main command available
```bash
make build

make start

make stop

make down

make restart

make ssh
make ssh-root
make ssh-nginx


make composer-install-no-dev
make composer-install
make composer-update

make info
make help

make logs
make logs-nginx

make phpunit
make report-code-coverage

make phpcs
make ecs
make ecs-fix
make phpmetrics
make phpcpd
make phpmd
make phpstan
make phpinsights

```
