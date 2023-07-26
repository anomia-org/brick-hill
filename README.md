# Laravel Site

## Information
Sadly as of now I am unable to provide a repository with the full git history of this project but I am working on cleaning it up so you can view all 4.2k commits created over the last 4 years.

## Prerequisites
 - [Docker](https://www.docker.com/get-started/)
 - Linux suggested but not required. Volume mounts on WSL are extremely slow and cause high load times

## Notes on Local Development
 - The renderer is an external service. All requests to the Renderer helper class will be ignored in local.

## Setup
1. Login to Gitlab registry

   ```
   docker login registry.gitlab.com
   ```
   Username is Gitlab username, password being a personal access token from [settings](https://www.docker.com/get-started/)

2. Rename .env.example to .env
3. Modify `APP_URL` and `SESSION_DOMAIN` to the domain you wish to use. Tests use `http://laravel-site.test`
4. Overwrite DNS to point your local computer to that domain. Linux is located at `/etc/hosts` macOS at `/private/etc/hosts` and Windows at `C:\Windows\System32\Drivers\etc\hosts`.

   ```
   127.0.0.1 laravel-site.test
   127.0.0.1 api.laravel-site.test
   127.0.0.1 admin.laravel-site.test
   ```

5. Run setup commands

   ```
   docker-compose run webpack npm install
   docker-compose run composer composer install
   docker-compose run app php artisan key:generate
   docker-compose run app php artisan migrate:fresh --seed
   ```

6. Site can be started with `docker-compose up -d`. Note that this will keep it always running and you will have to run `docker-compose down` to turn it off.
   - Optional services like the queue worker and scheduler can be started with `docker-compose --profile all-services up -d`
7. Default username and password are `Test Name` `password`

## Development Tools

Anyone not using VSCode is invalid.

Suggested extensions
 - [Laravel Artisan](https://marketplace.visualstudio.com/items?itemName=ryannaddy.laravel-artisan)
 - [Laravel Blade Formatter](https://marketplace.visualstudio.com/items?itemName=shufo.vscode-blade-formatter)
 - [Laravel Blade Snippets](https://marketplace.visualstudio.com/items?itemName=onecentlin.laravel-blade)
 - [PHP Intelephense](https://marketplace.visualstudio.com/items?itemName=bmewburn.vscode-intelephense-client)
 - [Volar](https://marketplace.visualstudio.com/items?itemName=johnsoncodehk.volar)
 - [Prettier](https://marketplace.visualstudio.com/items?itemName=esbenp.prettier-vscode)

Code completion suggestions for Intelephense can be written using
```
docker-compose run app php artisan ide-helper:model -M
docker-compose run app php artisan ide-helper:generate
```

## Running Tests

Laravel tests are run with `docker-compose run app php artisan test`

PHPStan tests are run with `docker-compose run app ./vendor/bin/phpstan analyse --memory-limit=2G`
