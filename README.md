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

### Estilo (PSR-12)

Dependência de desenvolvimento: **PHP-CS-Fixer** (regra `@PSR12` em `.php-cs-fixer.dist.php`).

```bash
composer lint        # só verifica (CI / antes do commit)
composer lint:fix    # corrige o que a ferramenta conseguir
```

Se `composer lint` terminar com código **8**, há arquivos fora do padrão — rode `lint:fix` ou ajuste na mão.

Ajuste o `.env` (banco e `JWT_SECRET`). Gere uma secret forte, por exemplo:

```bash
openssl rand -base64 32
```

Use **`APP_DEBUG=true`** só em desenvolvimento local: com esse modo, respostas **500** podem incluir a mensagem da exceção (útil no Insomnia). Em qualquer ambiente exposto, prefira **`APP_DEBUG=false`** para mensagem genérica e código `INTERNAL_ERROR` (ver [`docs/json-contract.md`](docs/json-contract.md)).

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

Erros JSON: `{ "error": { "message", "code", "errors?" } }`. Sucesso: `{ "data": ... }`. Detalhes em [`docs/json-contract.md`](docs/json-contract.md). Convenções de exceções (#23): [`docs/error-conventions.md`](docs/error-conventions.md).

---

## PSRs neste lab

O projeto segue [PHP-FIG PSRs](https://www.php-fig.org/psr/) onde faz sentido para um lab **PHP nativo** (sem virar Symfony). Objetivo: comparar depois com Laravel, que implementa ideias parecidas com nomes diferentes.

| PSR | Tema | Neste repositório |
|-----|------|-------------------|
| **PSR-4** | Autoload (namespace → pasta) | **Em uso** — `composer.json` mapeia `App\` → `src/` |
| **PSR-12** | Estilo de código | **Em uso (#13)** — [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) com regra `@PSR12`; `composer lint` (só verifica), `composer lint:fix` (aplica) |
| **PSR-11** | Container de DI (`ContainerInterface`) | **Em uso** — [PHP-DI](https://php-di.org/) em `bootstrap/container.php`; `app.php` monta o container e `handlers.php` resolve controllers com `$container->get(...)` |
| **PSR-7** | Mensagens HTTP (Request / Response) | **Em uso (#19, #21)** — Nyholm; `ServerRequest` + `ResponseInterface`; `requestJsonBody($request)`, `requestFilesBag($request)`; `emitResponse()` no `index` |
| **PSR-15** | Middleware + request handler | **Em uso (#20)** — `MiddlewarePipeline`, `CallableRequestHandler`, `AuthMiddleware` nas rotas protegidas; `user_id` / `role` em atributos do request |
| **PSR-15 (HTTP)** vs item **#15** do sprint | — | O backlog **#15** fala de **FastRoute vs `Router` caseiro**; não confundir com middleware PSR-15 |

**Por que não PSR-7/15 desde o início?** De propósito: ver rotas, PDO, validação e JSON sem abstrair HTTP de uma vez. Sprint 3 (PSR **11 → 7 → 15**) concluída no núcleo; API REST e contrato JSON iguais no Insomnia.

**Equivalente mental no Laravel (referência):** PSR-4 ≈ autoload Composer; PSR-11 ≈ service container; PSR-7 ≈ `Request` / `Response`; PSR-15 ≈ middleware (`auth`, etc.) antes do controller.

---

## Estrutura

- `public/index.php` — front controller
- `bootstrap/` — env, HTTP, auth, **container** (PSR-11), app, handlers
- `src/` — controllers, repositories, routing, services
- `database/schema.sql` — snapshot do schema
- `database/seeds/` — dados de dev
- `database/migrations/` — histórico de alterações

---

## Licença

Uso pessoal / estudo. Ajuste se publicar o repositório.
