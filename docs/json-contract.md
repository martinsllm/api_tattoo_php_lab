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

- Helpers em `bootstrap/http.php`: `jsonSuccess(int $status, mixed $data)`, `jsonError(int $status, string $message, string $code, ?array $fieldErrors = null)` e `requestJsonBody(): array` (leitura de `php://input` + decode).
- Controllers e o dispatch no `index` usam esses helpers em vez de montar o envelope manualmente.
