# API Tattoo PHP Lab

Laboratório em **PHP 8.4 nativo** (sem Laravel) inspirado no domínio da API **Tattoo Finder**. Objetivo: entender o que um framework costuma abstrair — rotas, HTTP, JSON, PDO, validação, auth com JWT.

Este repositório **não substitui** a API Laravel; é um espelho didático, evoluído aos poucos.

---

## Requisitos

- PHP 8.4+
- [Composer](https://getcomposer.org/)
- MySQL 8+ (Docker ou local)

---

## Setup da aplicação

```bash
git clone <url-do-repo>
cd api_tattoo_php_lab
composer install
cp .env.example .env
```

Ajuste o `.env` (banco e `JWT_SECRET`). Gere uma secret forte, por exemplo:

```bash
openssl rand -base64 32
```

---

## Banco de dados

### MySQL

O lab usa por padrão:

| Variável     | Exemplo              |
|-------------|----------------------|
| `DB_HOST`   | `127.0.0.1`          |
| `DB_PORT`   | `3307`               |
| `DB_NAME`   | `api_tattoo_php_lab` |
| `DB_USER`   | `api_tattoo`         |
| `DB_PASSWORD` | `secret`           |

Crie o usuário e permissões no MySQL conforme o seu ambiente (Docker, etc.). Este repositório não inclui `docker-compose`; use o MySQL que já tiver na porta configurada no `.env`.

### Instalação nova (clone do zero)

Ordem recomendada — **só estes dois passos** para um banco vazio:

1. **`database/schema.sql`** — cria o database e todas as tabelas no estado atual.
2. **`database/seeds/dev.sql`** *(opcional)* — dados de desenvolvimento (usuários, artistas vinculados, reviews).

```bash
mysql -h 127.0.0.1 -P 3307 -u api_tattoo -psecret < database/schema.sql
mysql -h 127.0.0.1 -P 3307 -u api_tattoo -psecret < database/seeds/dev.sql
```

Só estrutura, sem dados: execute apenas o `schema.sql`.

### Reset completo (dev)

```bash
mysql -h 127.0.0.1 -P 3307 -u api_tattoo -psecret -e "DROP DATABASE IF EXISTS api_tattoo_php_lab; CREATE DATABASE api_tattoo_php_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -h 127.0.0.1 -P 3307 -u api_tattoo -psecret < database/schema.sql
mysql -h 127.0.0.1 -P 3307 -u api_tattoo -psecret < database/seeds/dev.sql
```

### Dados de dev (`seeds/dev.sql`)

Senha de **todos** os usuários: `Senha12345`

| E-mail | Perfil | Notas |
|--------|--------|--------|
| `ana.artist@lab.test` | artist | Dona do artista **#1** (Ink Balneário) |
| `bruno.artist@lab.test` | artist | Dono do artista **#2** (Tinta Itajaí) |
| `carla.client@lab.test` | client | — |
| `diego.client@lab.test` | client | — |

Artista **#3** (Estúdio Órfão) existe com `user_id` NULL — útil para testar vínculo/permissões depois.

### Pasta `database/migrations/`

Arquivos **incrementais** para quem já tinha o lab numa versão antiga (ex.: adicionar `users`, `artists.user_id`, coluna `stored_path`).

Quem clona **hoje** e aplica o `schema.sql` **não** precisa rodar a fila 001→005 num banco vazio: o schema já reflete o estado final. Rodar migrations antigas de `INSERT` (001/002) em cima do seed atual **duplicaria** dados.

---

## Executar localmente

Use o roteador embutido apontando para o front controller (todas as rotas passam por `public/index.php`):

```bash
php -S 127.0.0.1:8081 public/index.php
```

Base URL: `http://127.0.0.1:8081`

---

## Rotas (resumo)

| Método | Caminho | Auth (Bearer JWT) |
|--------|---------|-------------------|
| GET | `/health` | Não |
| GET | `/artists`, `/artists/{id}` | Não |
| POST | `/auth/register`, `/auth/login` | Não |
| GET/POST | `/reviews`, `/reviews/{id}` | Sim |
| GET/POST | `/artists/{id}/portfolio`, `/artist-images/{id}` | Sim |

Login: `POST /auth/login` com JSON `email` e `password` → resposta inclui `token`. Demais rotas protegidas: header `Authorization: Bearer <token>`.

Erros JSON: `{ "error": { "message", "code", "errors?" } }`. Sucesso: `{ "data": ... }`.

---

## Estrutura

- `public/index.php` — front controller
- `bootstrap/` — env, HTTP, auth, app, handlers
- `src/` — controllers, repositories, routing, services
- `database/schema.sql` — snapshot do schema
- `database/seeds/` — dados de dev
- `database/migrations/` — histórico de alterações

---

## Licença

Uso pessoal / estudo. Ajuste se publicar o repositório.
