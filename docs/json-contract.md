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
| `VALIDATION_ERROR` | 422  | Payload inválido (previsto para `POST /reviews`) |

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

**Validação (futuro POST /reviews) — 422**

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

## Implementação (referência)

- Helpers planejados em `bootstrap/http.php`: `jsonSuccess(int $status, mixed $data)` e `jsonError(int $status, string $message, string $code, ?array $fieldErrors = null)`.
- Controllers e o dispatch no `index` devem usar esses helpers em vez de montar o envelope manualmente.
