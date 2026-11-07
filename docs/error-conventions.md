# Convenções de erro HTTP (#23 — fatia D)

Este documento complementa [`json-contract.md`](json-contract.md): **quando lançar exceção** vs **quando ainda montar `Response` no handler/middleware**, e um checklist rápido de regressão.

## Camadas

| Camada | Responsabilidade |
|--------|------------------|
| **`ExceptionMiddleware`** | `HttpException` → `jsonErrorResponse`; outro `Throwable` → **500** `INTERNAL_ERROR` |
| **`AuthMiddleware`** | JWT ausente/inválido → **401** `UNAUTHORIZED` (continua `return jsonErrorResponse` aqui) |
| **Validators** (`App\Validation\*`) | Regras de payload; falha → `throw ValidationFailedException` (**nunca** `jsonErrorResponse`) |
| **Controllers** | Orquestram; erros previstos → `throw` exceções `App\Exception\*`; sucesso → `return jsonResponse` |

## Quando usar `throw` (exceções do lab)

Use **`throw`** para erros **previstos** que o middleware já sabe traduzir em JSON:

| Situação | Exceção típica |
|----------|----------------|
| Recurso não encontrado (por `error.code` no contrato) | `NotFoundException` (`ARTIST_NOT_FOUND`, `REVIEW_NOT_FOUND`, …) |
| Sem permissão (dono / role) | `ForbiddenException` (`FORBIDDEN`) |
| Validação de campos | `ValidationFailedException` (`VALIDATION_ERROR` + `error.errors`) |
| Body JSON inválido | `InvalidJsonException` (`INVALID_JSON`) — via `requestJsonBody()` |
| Falha ao persistir upload (após validação) | `UploadFailedException` (`UPLOAD_FAILED`) |

Hierarquia: todas estendem **`App\Exception\HttpException`** (`statusCode()`, `errorCode()`, `fieldErrors()` opcional).

## Quando ainda usar `return jsonErrorResponse`

| Onde | Motivo |
|------|--------|
| **`public/index.php`** | Rota/handler inexistente (fora do pipeline de controllers) |
| **`AuthMiddleware`** | **401** Bearer/JWT — já estabelecido no #20; fora do inventário de controllers |
| **`AuthController` (transição)** | **409** e **401** login ainda retornam `jsonErrorResponse` — ver seção [Auth 409/401](#auth-409401--onde-migrar) abaixo |

Meta após polish: **zero** `return jsonErrorResponse` nos controllers do inventário #23.

## Validators

- Assinatura comum: **`validate(array $data): array`** (`App\Validation\Validator`).
- **JSON** (auth, reviews): `$data` = array decodificado do body.
- **Multipart (portfolio):** o controller chama `normalizeFiles` e passa **`['items' => $listaDeArquivos]`**; o validator só garante “pelo menos um arquivo” e devolve a **lista** para o `foreach`.

## Upload no `store` do portfolio

- `UploadService` lança `\InvalidArgumentException` / `\RuntimeException` (PHP genérico).
- O **controller** traduz para exceções do lab (não mapear `InvalidArgumentException` global no middleware).
- `InvalidArgumentException` → `ValidationFailedException` com chave `images`.
- `RuntimeException` → `UploadFailedException`.

## Auth 409/401 — onde migrar

Ainda **não** migrados (C1 deixou de propósito):

| Arquivo | Situação | Hoje | Destino sugerido |
|---------|----------|------|------------------|
| `src/Controllers/AuthController.php` | Email duplicado no register | `return jsonErrorResponse(409, …, 'EMAIL_ALREADY_EXISTS')` | `throw` exceção **409** (ex.: `ConflictException` com code parametrizado) |
| `src/Controllers/AuthController.php` | Credenciais inválidas no login (2 ramos) | `return jsonErrorResponse(401, …, 'INVALID_CREDENTIALS')` | `throw` exceção **401** (ex.: `UnauthorizedException` ou `InvalidCredentialsException` com `INVALID_CREDENTIALS`) |

**Não confundir** com **401** `UNAUTHORIZED` do **`AuthMiddleware`** (token ausente/inválido) — esse fica no middleware.

Passos ao migrar (mentoria):

1. Criar exceção(s) `extends HttpException` (409 com `errorCode` parametrizável, como `NotFoundException`).
2. Substituir os três `return jsonErrorResponse` no `AuthController` por `throw`.
3. Insomnia: register duplicado, login senha errada, login inexistente — mesmos status/body.

## Checklist Insomnia / curl (regressão #23)

Marcar após mudanças na fatia C ou auth:

- [ ] `GET /health` → 200
- [ ] `GET /artists/999` → 404 `ARTIST_NOT_FOUND`
- [ ] `POST /auth/register` `{}` → 422 `VALIDATION_ERROR`
- [ ] `POST /auth/login` credencial inválida → 401 `INVALID_CREDENTIALS`
- [ ] `GET /reviews/999` (Bearer client) → 404 `REVIEW_NOT_FOUND`
- [ ] `POST /reviews` como artist → 403 `FORBIDDEN`
- [ ] `POST /reviews` payload inválido → 422
- [ ] `GET /artists/999/portfolio` (Bearer) → 404 `ARTIST_NOT_FOUND`
- [ ] `GET /artist-images/999` (Bearer) → 404 `ARTIST_IMAGE_NOT_FOUND`
- [ ] `POST /artists/1/portfolio` sem arquivo (dono) → 422 `images`
- [ ] `POST /artists/1/portfolio` arquivo inválido → 422 `images`
- [ ] `POST /artists/1/portfolio` JPEG ok → 201
- [ ] Rota inexistente → 404 `ROUTE_NOT_FOUND`

## Commits de referência (#23)

Ver `sprint.md` — fatias A–D e passes C0–C4.
