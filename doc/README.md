# Documentació

- [IA x Desenvolupar](./prompts-log.md)
- DeepWiki: https://deepwiki.com/inspedralbes/prj-entrades-a23cliferand

## Dades d'accés

- Email: client@client.com / Password: Jupiter1
- Email: admin@admin.com / Password: Jupiter1

### Objectius

`Cinema Paradise` és una plataforma de gestió i compra d'entrades de cinema amb aquests objectius principals:

- Consultar cartellera i sessions disponibles.
- Fer reserva de seients en temps real (incloent convidats sense compte).
- Confirmar compres i consultar entrades de l'usuari.
- Proporcionar panell d'administració per gestionar dades i estadístiques.

### Arquitectura bàsica

#### Diagrama database

![Diagrama de la base de dades](./diagrama-db.png)

#### Tecnologies utilitzades

- **Frontend**: Nuxt 3 (Vue 3), Pinia, Cypress.
- **Backend API**: Laravel 13 + PHP 8.3, Sanctum per autenticació.
- **Temps real**: Node.js + Socket.IO.
- **Persistència**: PostgreSQL.
- **Capa de cache/dades ràpides**: Redis.
- **Infraestructura**: Docker Compose (`DEV` i `PROD`) + Nginx en producció.

#### Interrelació entre components

1. El frontend (`front`) consumeix l'API REST de Laravel (`back/laravel-api`).
2. Laravel treballa amb PostgreSQL per dades de negoci (usuaris, reserves, sessions, etc.).
3. Laravel i Node publiquen/escolten esdeveniments per actualitzar estat de seients en temps real.
4. Redis es fa servir per cache/sincronització de pel·lícules i dades auxiliars.
5. En producció, Nginx fa de punt d'entrada i proxy cap als serveis interns.

### Com crees l'entorn de desenvolupament

Des de l'arrel del repositori:

```bash
docker compose -f compose.DEV.yml up --build
```

Quan els contenidors estiguin aixecats, inicialitza dades del backend:

```bash
docker compose exec tr3-cf-laravel-api php artisan migrate:fresh --seed
```

Serveis habituals en desenvolupament:

- Frontend Nuxt: `http://localhost:3000`
- API Laravel: `http://localhost:8000`
- Node (socket): `http://localhost:3001`
- pgAdmin: `http://localhost:8080`
- Redis Commander: `http://localhost:8081`

### Com desplegues l'aplicació a producció

El desplegament està definit a `compose.PROD.yml` amb aquests serveis:

- `tr3-cf-nginx` (reverse proxy i TLS)
- `tr3-cf-laravel-api` (PHP-FPM)
- `tr3-cf-node` (socket server)
- `tr3-cf-postgres` i `tr3-cf-redis`
- `tr3-cf-certbot` (renovació de certificats)

Passos generals:

1. Configurar variables d'entorn de producció (contrasenyes, tokens, etc.).
2. Generar certificats inicials amb `certbot` (comentat al fitxer compose).
3. Arrencar serveis:

```bash
docker compose -f compose.PROD.yml up -d --build
```

URL pública indicada al projecte: `https://cinema.winewithcola.com`.

### Llistat d'endpoints de l'API de backend

Base path habitual: `/api` (Laravel).

#### Rutes

##### Autenticació

- `POST /api/auth/register`
- `POST /api/auth/login`
- `POST /api/auth/logout` (auth)
- `GET /api/user` (auth)

##### Entitats CRUD

- `GET/POST/PUT/DELETE /api/usuaris{/{id}}` (POST/PUT/DELETE admin)
- `GET/POST/PUT/DELETE /api/pelicules{/{id}}` (POST/PUT/DELETE admin)
- `GET/POST/PUT/DELETE /api/reserves{/{id}}`
- `GET/POST/PUT/DELETE /api/sessions{/{id}}` (POST/PUT/DELETE admin)
- `GET /api/sessions/pelicula/{peliculaId}`
- `GET/POST/PUT/DELETE /api/sales{/{id}}` (POST/PUT/DELETE admin)
- `GET/POST/PUT/DELETE /api/tarifes{/{id}}` (POST/PUT/DELETE admin)
- `GET /api/tarifes/tipus-client`

##### Reserva i entrades

- `GET /api/sessions/{sessioId}/seients`
- `GET /api/reserves/usuari/{usuariId}/sessio/{sessioId}`
- `POST /api/reserves/seient_reservar`
- `POST /api/reserves/seient_desocupar`
- `POST /api/reserves/confirmar`
- `POST /api/reserves/transferir-guest` (auth)
- `POST /api/reserves/expirar`
- `GET /api/entrades/les-meves` (auth)

##### Admin

- `GET /api/admin/stats` (auth + admin)
- `POST /api/pelicules/sync/all` (auth + admin)
- `POST /api/pelicules/sync/{imdbId}` (auth + admin)

#### Exemples de JSON de petició

**Login** `POST /api/auth/login`

```json
{
  "email": "admin@admin.com",
  "password": "secret123"
}
```

**Reservar seients (bloqueig temporal)** `POST /api/reserves/seient_reservar`

```json
{
  "sessio_id": 12,
  "seient_ids": [101, 102],
  "usuari_id": "guest_1712345678901"
}
```

**Confirmar compra** `POST /api/reserves/confirmar`

```json
{
  "sessio_id": 12,
  "usuari_id": "guest_1712345678901",
  "email": "client@example.com",
  "seients": [
    { "id": 101, "tipus_client_id": 1, "preu_aplicat": 8.5 },
    { "id": 102, "tipus_client_id": 1, "preu_aplicat": 8.5 }
  ],
  "total": 17.0
}
```

#### Exemples de JSON de resposta i codis d'estat

**200 OK** `POST /api/auth/login`

```json
{
  "message": "Login correcte",
  "token": "1|xxxxxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "nom": "Admin",
    "email": "admin@admin.com",
    "rol": "admin"
  }
}
```

**201 Created** `POST /api/auth/register`

```json
{
  "message": "Usuari registrat correctament",
  "token": "2|xxxxxxxxxxxxxxxx",
  "user": {
    "id": 44,
    "nom": "Nou Usuari",
    "email": "nou@exemple.com",
    "rol": "client"
  }
}
```

**404 Not Found** `GET /api/pelicules/{id}`

```json
{
  "error": "Pel·lícula no trobada"
}
```

**400 Bad Request** `POST /api/reserves/seient_reservar`

```json
{
  "error": "El seient A5 ja no està disponible"
}
```

### Altres elements importants

- **Seguretat**: ús de tokens amb Laravel Sanctum (`auth:sanctum`) i middleware `admin` / `auth` en rutes restringides.
- **Tests frontend**: suite de Cypress a `front/cypress`.
- **Tests backend**: suite de PHPUnit a `backend/tests`.
- **Gestió de projecte**: Taiga i disseny amb Penpot (enllaços al [README.md](../README.md) arrel).
