# Send SMS API Integration

This document outlines how to integrate SMS sending functionality into your application using the Skypush API.

## Overview

**Base URL**: `https://pushsms.rehospace.com`  
**Endpoint**: `/api/v1/send`  
**Method**: `POST`

## Authentication

All API requests require an API Key to be passed in the headers.

**Header Name**: `X-API-KEY`  
**Value**: *Your Internal API Key* (Contact System Administrator to obtain this)

## Request Specification

### Headers
| Header | Value | Required |
|--------|-------|----------|
| `Content-Type` | `application/json` | Yes |
| `Accept` | `application/json` | Yes |
| `X-API-KEY` | `<YOUR_SECRET_KEY>` | Yes |

### Body Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `to` | String | Yes | Comma-separated phone numbers (e.g. `2557xxxx,2557xxxx`). |
| `message` | String | Yes | The SMS content. Max 1000 chars. |
| `client_app` | String | No | Identifier for the calling application (e.g. `HMS`, `CRM`). |
| `reference` | String | No | Unique internal reference ID for tracking. |
| `sender` | String | No | Custom Sender ID (max 11 chars). Defaults to system default if omitted. |
| `language` | String | No | `English` (default) or `Unicode`. |
| `scheduled_at` | String | No | Date/Time to schedule the message (format: `YYYY-MM-DD HH:mm:ss`). |

### Example Request (cURL)

```bash
curl -X POST https://pushsms.rehospace.com/api/v1/send \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-API-KEY: your_super_secret_key" \
  -d '{
    "to": "255712345678,255787654321",
    "message": "Your OTP is 123456",
    "client_app": "BillingSystem",
    "sender": "SKYLINK"
  }'
```

### Example Request (PHP/Guzzle)

```php
$client = new \GuzzleHttp\Client();
$response = $client->post('https://pushsms.rehospace.com/api/v1/send', [
    'headers' => [
        'X-API-KEY' => 'your_super_secret_key',
        'Accept'    => 'application/json',
    ],
    'json' => [
        'to'         => '255712345678',
        'message'    => 'Your OTP is 123456',
        'client_app' => 'BillingSystem',
        'sender'     => 'SKYLINK' // Optional
    ]
]);

$body = json_decode($response->getBody(), true);
// $body['id'] contains the message ID
```

## Response Specification

### Success Response (201 Created)

```json
{
    "status": "ok",
    "id": 154
}
```

### Error Response (422 Unprocessable Entity)
Occurs when validation fails (e.g., missing 'to' or 'message').

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "to": [
            "The to field is required."
        ]
    }
}
```

### Error Response (401 Unauthorized)
Occurs when the API Key is missing or incorrect.

```json
{
    "message": "Unauthorized"
}
```

## Additional Endpoints

### Check Balance
**Endpoint**: `GET /api/v1/balance`  
Returns the current SMS balance from the provider.

### Check Sender Balance
**Endpoint**: `GET /api/v1/sender-balance?username={username}`  
Returns the balance for a specific sender account.
