Task management API

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Passo a passo

1. Clone repositório e acesse o projeto

```bash
git clone https://github.com/romesdev/task-management-api
```

```
cd task-management-api/
```

2. Crie o arquivo .env e preencha conforme o .env.example (ou copie os valores abaixo)

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

3. Suba os containers do projeto

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
9. Acesse a documentação do projeto em <http://localhost:8989/api/documentation>
![api-docs](docs/images/swagger.png)

10. Faça o registro de um usuário
![register](docs/images/register.png)

11. Faça o login com o usuário criado
![alt text](docs/images/input-login.png)

12. Recupere o `token` de autenticação
![alt text](docs/images/token-login.png)

13. Vá em `authorize`, cole o `token` e clique em `authorize`
![api-docs](docs/images/swagger.png)
![alt text](docs/images/authorize.png)

14. Agora, você pode acessar as rotas protegidas (`/api/tasks`)

15. Listando tarefas de forma paginada
![alt text](docs/images/list-params.png)
![alt text](docs/images/list-data.png)
![alt text](docs/images/metadata.png)

16. Criando uma nova tarefa
![alt text](docs/images/input-task.png)
![alt text](docs/images/task-created.png)

17. Recuperando a tarefa recém criada
![alt text](docs/images/task-id.png)
![alt text](docs/images/task-details.png)

18. Tentando atualizar a tarefa de forma errada
![alt text](docs/images/wrong-input.png) 
Repare que a data de vencimento deve ser uma data igual a hoje ou futura. A nossa aplicação tem validação para todos os campos e com tradução. 
![alt text](docs/images/invalid-date.png)

19. Atualizando a tarefa com sucesso
![alt text](docs/images/input-update.png)
![alt text](docs/images/task-updated.png)

20. Deletando a tarefa 
![alt text](docs/images/input-delete.png)
Ao final obtemos sucesso com o status code 204.
![alt text](docs/images/delete-response.png) 

## OBS: A nossa aplicação tem validação para todos os campos e com tradução. 

![alt text](docs/images/example-wrong-input.png)
![alt text](docs/images/validation-example.png)

21. Tentando cadastrar um usuário com e-mail duplicado
![alt text](docs/images/duplicate-email.png)
![alt text](docs/images/duplicate-error.png)

### Outra forma de testar a aplicação é rodando os testes
```bash
php artisan test
```