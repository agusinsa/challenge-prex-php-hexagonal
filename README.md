# API Giphy

API REST para buscar GIFs en Giphy, ver uno por id y guardarlo como favorito. El acceso usa un token de Passport (OAuth 2.0) que vence a los 30 minutos. Cada request de la API queda en `api_logs`, con la contraseña del login reemplazada por `***`.

## Requisitos

- Docker
- Una clave de la API de Giphy

Los puertos `8000` y `3306` tienen que estar libres.

## Configuración

```bash
cp .env.example .env
```

Completá `GIPHY_API_KEY` en `.env`.

Si `APP_KEY` está vacío, generala antes de levantar los contenedores:

```bash
docker compose build
docker compose run --rm --entrypoint php app artisan key:generate
```

`.env` deja `DB_HOST=127.0.0.1` para conectarse desde la máquina. El contenedor de la API usa el host `db`, definido en `docker-compose.yml`.

## Levantar

```bash
docker compose up --build -d
```

- API: `http://127.0.0.1:8000`
- MySQL 8.4: `127.0.0.1:3306`, base `giphy`, usuario `giphy`, clave `secret`. Root: `root` / `root`.

Al arrancar, la API genera las claves de Passport y corre las migraciones. Las tablas quedan en el volumen `dbdata`.

## Primera vez

El cliente de Passport se crea una sola vez:

```bash
docker compose exec app php artisan passport:client --personal --name="login" --provider=users --no-interaction
```

Y un usuario para probar el login:

```bash
docker compose exec app php artisan tinker --execute="App\Models\User::query()->updateOrCreate(['email' => 'ada@example.com'], ['name' => 'Ada', 'password' => 'secret']);"
```

## Endpoints

| Método | Ruta | Auth | Respuesta |
| --- | --- | --- | --- |
| POST | `/api/login` | No | `200` con `token` y `expires_in`. `401` si las credenciales no sirven. |
| GET | `/api/gifs?query=gato&limit=1&offset=0` | Bearer | `200` con la lista. `query` es obligatorio. |
| GET | `/api/gifs/{id}` | Bearer | `200` con el GIF. `404` si Giphy no lo tiene. |
| POST | `/api/favorites` | Bearer | `201` al guardar. Body: `gif_id`, `alias`, `user_id`. |

`user_id` tiene que ser el usuario del token. Si es otro, responde `403`. Si ese usuario ya guardó ese GIF, responde `409`. Sin token, las rutas protegidas responden `401`. Un body incompleto responde `422`.

El id de Giphy es un string.

## Postman

Importá los dos archivos y elegí el environment **local**:

- `postman/API-Giphy.postman_collection.json`
- `postman/local.postman_environment.json`

Corré **login** primero. El script guarda el token y las otras requests lo envían como Bearer. **buscar gifs** actualiza `gif_id` con el primer resultado.

## Tests

No necesitan MySQL ni Giphy. El log se descarta y los puertos se reemplazan por falsos.

```bash
php artisan test
```

## Arquitectura

El dominio no usa Laravel. La aplicación tiene los casos de uso. Infraestructura implementa los puertos: Eloquent, el HTTP de Giphy, Passport y los controllers. El `bind` está en `AppServiceProvider`.
