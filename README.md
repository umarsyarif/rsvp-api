## Laravel Login

Sistema de login simples onde vai ser usado Laravel + Flutter
Vai ser utilizado Sanctum para autenticação

## Links

- **[Laravel7x](https://laravel.com/docs/7.x/installation)**
- **[Sanctum](https://laravel.com/docs/7.x/sanctum#introduction)**

## LARAVEL API URLS

api/auth/register - POST

    NAME                   - Required | min:3 | Max:60
    EMAIL                  - Required | Email | Min:3 | Max:60 | Unique: Users,
    PASSWORD               - Required | Confirmed | Min:6| Max:16                   Confirmed // password_confirmation

api/auth/token - POST

    EMAIL                  - Required | Email | Min:3 | Max:60,
    PASSWORD               - Required | Min:6| Max:16

api/auth/logout - POST

    Authenticated - Bearer Token

api/auth/me - GET

    Authenticated - Bearer Token