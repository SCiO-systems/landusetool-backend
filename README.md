# SCiO LUP4LDN

The LUP4LDN project.

## REQUIREMENTS

The project requires **[PHP 7.4/8.0](https://www.php.net/manual/en/install.php)** as well as **[Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)** to be installed.

## INSTALL THE DEPENDENCIES

    cd scio-lup4ldn-backend && composer install

## BUILD THE CUSTOM DOCKER IMAGE

    export WWWUSER=${WWWUSER:-$UID}
    export WWWGROUP=${WWWGROUP:-$(id -g)}
    cd docker
    docker build -t sail-8.0/app . --build-arg WWWGROUP=$WWWGROUP --platform linux/amd64

**NOTE**: The custom docker image includes the scheduler for running Laravel scheduled tasks.
**NOTE**: The custom docker image includes the workers for running Laravel jobs using the listen
command so that they always get updated to use the latest code changes.
**NOTE**: The custom docker image is only meant to be used in development environments.

## CONFIGURE

    cp .env.example .env

## RUN THE BACKEND

    ./vendor/bin/sail up -d

## STOP THE BACKEND

    ./vendor/bin/sail down

## GENERATE A KEY AND A JWT SECRET

    ./vendor/bin/sail artisan key:generate
    ./vendor/bin/sail jwt:secret

## RUN THE MIGRATIONS

To destroy the existing database and start fresh:

    ./vendor/bin/sail artisan migrate:fresh

## SEED THE DATABASE

To seed the database with test data:

    ./vendor/bin/sail artisan db:seed

**NOTE**: In order to be able to seed the data, you need to modify your **.env** file and set your application environment to the following `APP_ENV=local` or `APP_ENV=development`.

### LINK THE STORAGE FOR FILES

To link the storage for files:

    ./vendor/bin/sail artisan storage:link

**NOTE**: The storage for files should be linked in order for the backend to be able to serve files publicly.

### CHANGE THE QUEUE DRIVER TO REDIS

To change the queue driver to redis, add the following settings:

    QUEUE_CONNECTION=redis
    REDIS_HOST=redis

### SET THE CACHE CONFIGURATION

To set the cache configuration you need to change the following settings:

    CACHE_TTL_SECONDS=3600

The default cache TTL of all the values is 1hr. The max cache recommended TTL is 12hr.

### EXTERNAL SERVICE CONFIGURATION

To set the configuration for the third-party services (like SCiO or other):

    SCIO_SERVICES_BASE_API_URL=
    SCIO_SERVICES_CLIENT_ID=
    SCIO_SERVICES_SECRET=
    SCIO_CACHE_TOKEN_KEY=scio_token

## RUN TESTS

To execute the test suites run:

    php artisan test

## AUTOCOMPLETION

If you add a dependency, `_ide_helper.php` will be auto-regenerated. If you change a model definition please run:

    php artisan ide-helper:models

and type `no` to update the model autocompletion file (`_ide_helper_models.php`).
