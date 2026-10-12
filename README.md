# API Tattoo PHP Lab

Laboratório em **PHP 8.4 nativo** (sem Laravel) inspirado no domínio da API **Tattoo Finder** (`api_tattoo`, Laravel). Objetivo: entender o que um framework costuma abstrair — rotas, HTTP, JSON, PDO, validação.

Este repositório **não substitui** a API Laravel; é um espelho didático, evoluído aos poucos.

---

## Requisitos

- PHP 8.4+
- [Composer](https://getcomposer.org/)
- MySQL (Docker ou local) — previsto a partir do item de persistência; ainda não usado no código atual

---

## Setup

```bash
git clone <url-do-repo>
cd api_tattoo_php_lab
composer install
```

---

## Executar localmente

Na raiz do projeto:

```bash
php -S 127.0.0.1:8080 -t public
```

Abra `http://127.0.0.1:8080/` no navegador ou use um cliente HTTP.

> Hoje o servidor embutido com `-t public` atende apenas a **raiz** (`/`). Roteamento (`/health`, etc.) está previsto no roadmap.

---

## O que já existe

| Método | Caminho | Resposta |
|--------|---------|----------|
| GET | `/` | `{"ok":true}` (`application/json`) |

Estrutura mínima:

- `public/index.php` — front controller
- `src/` — classes (`App\`, PSR-4 via Composer)
- `composer.json` — PHP ^8.4 e autoload

---

## Roadmap (v1)

- Roteamento mínimo (`GET /health`, 404 JSON)
- PDO + MySQL (database dedicado ao lab)
- `GET /artists`, `GET /artists/{id}`
- `POST /reviews` com validação manual e 422

---

## Licença

Uso pessoal / estudo. Ajuste se publicar o repositório.
