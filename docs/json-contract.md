# Contrato JSON — API Tattoo PHP Lab (v1)

Contrato HTTP/JSON do lab (PHP nativo). Objetivo: respostas previsíveis e comparáveis com `api_tattoo` (Laravel), sem copiar API Resources.

## Regras

1. **Sucesso (2xx):** o corpo é sempre `{ "data": … }`. O valor de `data` pode ser um objeto, um array ou, no health check, `{ "ok": true }`.
2. **Erro (4xx/5xx):** o corpo é sempre `{ "error": { "message": "…", "code": "…" } }`. Em validação (**422**), incluir `"errors": { "campo": ["…"] }` dentro de `error`.
3. **HTTP:** o status code define sucesso ou falha; toda resposta de API usa `Content-Type: application/json; charset=utf-8`.

## Códigos de erro (v1)

| code             | HTTP | Quando usar                                      |
| ---------------- | ---- | ------------------------------------------------ |
| `ROUTE_NOT_FOUND`  | 404  | Rota ou handler inexistente                      |
| `ARTIST_NOT_FOUND` | 404  | `GET /artists/{id}` sem registro no banco        |
| `VALIDATION_ERROR` | 422  | Payload inválido em `POST /reviews`              |
| `INVALID_JSON`     | 400  | Body não é JSON válido ou não é um objeto JSON     |

## Exemplos — sucesso

**GET /health — 200**

```json
{
  "data": {
    "ok": true
  }
}
```

**GET /artists — 200**

```json
{
  "data": [
    {
      "id": 1,
      "studio_name": "Studio 1",
      "city": "Balneario Camboriu",
      "state": "SC",
      "created_at": "2026-10-15 00:06:38"
    }
  ]
}
```

**GET /artists/1 — 200**

```json
{
  "data": {
    "id": 1,
    "studio_name": "Studio 1",
    "city": "Balneario Camboriu",
    "state": "SC",
    "created_at": "2026-10-15 00:06:38"
  }
}
```

**POST /reviews — 201**

Body: `{ "artist_id": 1, "rating": 5, "comment": "…" }` (`comment` opcional).

```json
{
  "data": {
    "id": 6,
    "artist_id": 1,
    "rating": 5,
    "comment": "Ótimo!"
  }
}
```

Sem `comment` no body, `"comment": null` em `data`.

## Exemplos — erro

**Rota desconhecida — 404**

```json
{
  "error": {
    "message": "Not Found",
    "code": "ROUTE_NOT_FOUND"
  }
}
```

**Artista inexistente — 404**

```json
{
  "error": {
    "message": "Artist not found",
    "code": "ARTIST_NOT_FOUND"
  }
}
```

**POST /reviews — validação — 422**

```json
{
  "error": {
    "message": "Validation failed",
    "code": "VALIDATION_ERROR",
    "errors": {
      "rating": ["Rating must be between 1 and 5."]
    }
  }
}
```

**POST /reviews — JSON inválido — 400**

```json
{
  "error": {
    "message": "Invalid JSON body",
    "code": "INVALID_JSON"
  }
}
```

## Implementação (referência)

Respostas JSON seguem [PSR-7](https://www.php-fig.org/psr/psr-7/) via [Nyholm/psr7](https://github.com/Nyholm/psr7). O **envelope** (`data` / `error`) não muda; só a forma de montar e enviar a resposta.

### Helpers — `bootstrap/http.php`

| Função | Retorno | Uso |
|--------|---------|-----|
| `jsonResponse(int $status, array $data)` | `ResponseInterface` | Sucesso 2xx — corpo `{ "data": … }` |
| `jsonErrorResponse(int $status, string $message, string $code, ?array $fieldErrors = null)` | `ResponseInterface` | Erro 4xx/5xx — corpo `{ "error": … }`; `$fieldErrors` vira `error.errors` (422) |
| `emitResponse(ResponseInterface $response)` | `void` (termina com `exit`) | Envia status, headers e body ao SAPI (PHP built-in server, FPM, etc.) |
| `requestJsonBody(ServerRequestInterface $request): array` | `array` | Body JSON do request PSR-7; inválido → **400** `INVALID_JSON` |
| `requestFilesBag(ServerRequestInterface $request): array` | `array` | Uploads via `getUploadedFiles()` (`bootstrap/uploads.php`) |

### Fluxo

1. **`public/index.php`** — `createServerRequest()`; roteamento; 404 com `emitResponse(jsonErrorResponse(...))`; rotas protegidas passam por **`AuthMiddleware`** (PSR-15); `$response = $pipeline->handle($request)`; `emitResponse($response)`.
2. **Controllers** — `return jsonResponse(...)` ou `return jsonErrorResponse(...)`; rotas autenticadas leem **`$request->getAttribute('user_id')`** e **`role`** (403 no handler quando aplicável).
3. **`App\Http\AuthMiddleware`** — JWT Bearer; **401** retorna `jsonErrorResponse` (sem `emitResponse`); sucesso enriquece o request com atributos.

Respostas **não JSON** (ex.: imagem em `GET /artist-images/{id}`) usam `Response` com `Content-Type` e stream do arquivo; o mesmo `emitResponse` no `index`.
