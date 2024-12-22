Task management API

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

### Passo a passo

1. Clone repositório e acesse o projeto:

```bash
git clone https://github.com/romesdev/task-management-api
```

```
cd task-management-api/
```

2. Crie o arquivo .env e preencha conforme o .env.example (ou copie os valores abaixo):

```.env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:nHBk7+tGlE74nM/0Ho/BKFQbgtEZIOFEBRiQz24MPeQ=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8989

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root

L5_SWAGGER_CONST_HOST=http://project.test/api/v1

```

3. Suba os containers do projeto:

```
docker-compose up -d
```

4. Acesse a linha do comando do container

```
docker-compose exec app bash
```

5. Instale as depedências do projeto

```
composer install
```

6. Gere a key do projeto laravel

```
php artisan key:generate
```

7. Rode as migrations
```
php artisan migrate
```

8. Rode o seeder
```
php artisan db:seed
```
9. Acesse a documentação do projeto em <http://localhost:8989/api/documentation>.
