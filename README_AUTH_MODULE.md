# Módulo de Autenticación - Laravel Sanctum

Este módulo implementa autenticación completa usando Laravel Sanctum siguiendo los principios de Domain Driven Design (DDD) y Clean Architecture.

## Características

- ✅ **Laravel Sanctum**: Autenticación por tokens
- ✅ **Expiración de 24 horas**: Tokens con vigencia configurable
- ✅ **Eliminación de tokens anteriores**: Al hacer login se eliminan tokens previos
- ✅ **Arquitectura Limpia**: Separación de capas (Domain, Application, Infrastructure)
- ✅ **DDD**: Value Objects, DTOs, UseCases
- ✅ **API Versionada**: Rutas bajo `/api/v1/auth/`
- ✅ **Testeable**: Tests unitarios y de feature

## Estructura del Módulo

```
app/
├── Domain/
│   └── Auth/
│       └── AuthServiceInterface.php    # Interfaz del servicio de auth
├── Application/
│   └── Auth/                          # Casos de uso
├── Infrastructure/
│   └── Auth/
│       └── AuthService.php            # Implementación con Sanctum
├── UseCases/
│   └── Auth/
│       ├── LoginUseCase.php
│       ├── LogoutUseCase.php
│       ├── LogoutAllTokensUseCase.php
│       └── RefreshTokenUseCase.php
├── DTOs/
│   └── Auth/
│       ├── LoginDTO.php
│       └── AuthResponseDTO.php
├── ValueObjects/
│   └── Auth/
│       └── Token.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   │           └── Auth/
│   │               └── AuthController.php
│   └── Requests/
│       └── Auth/
│           └── LoginRequest.php
└── Providers/
    └── AuthServiceProvider.php
```

## Endpoints

### Login
```
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123"
}
```

**Respuesta:**
```json
{
    "message": "Login successful",
    "data": {
        "token": "1|abc123...",
        "token_type": "Bearer",
        "expires_in": 86400,
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com"
        }
    }
}
```

### Logout (Token actual)
```
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

### Logout All Sessions
```
POST /api/v1/auth/logout-all
Authorization: Bearer {token}
```

### Refresh Token
```
POST /api/v1/auth/refresh
Authorization: Bearer {token}
```

### Get Current User
```
GET /api/v1/auth/me
Authorization: Bearer {token}
```

## Configuración

### Sanctum Configuration
El token tiene una expiración de 24 horas configurada en `config/sanctum.php`:

```php
'expiration' => 1440, // 24 hours (24 * 60 = 1440 minutes)
```

### Service Provider
El módulo se registra automáticamente a través de `AuthServiceProvider` que:
- Registra el binding del servicio de autenticación
- Permite inyección de dependencias

## Casos de Uso

### LoginUseCase
- Valida credenciales del usuario
- Verifica que el usuario no esté eliminado
- **Elimina todos los tokens anteriores del usuario**
- Genera nuevo token con expiración de 24 horas
- Retorna datos del usuario y token

### LogoutUseCase
- Elimina el token actual del usuario
- Invalida la sesión específica

### LogoutAllTokensUseCase
- Elimina todos los tokens del usuario
- Cierra todas las sesiones activas

### RefreshTokenUseCase
- Valida el token actual
- Genera nuevo token con nueva expiración
- Elimina el token anterior

## Value Objects

### Token
- Encapsula la lógica del token de autenticación
- Soporta token hasheado y texto plano
- Validación de token no vacío
- Métodos de comparación

## Seguridad

### Eliminación de Tokens Anteriores
Cada vez que un usuario hace login:
1. Se eliminan **todos los tokens anteriores** del usuario
2. Se genera un nuevo token con expiración de 24 horas
3. Solo se mantiene una sesión activa por usuario

### Expiración Automática
- Los tokens expiran automáticamente después de 24 horas
- No es necesario hacer logout manual
- Los tokens expirados no pueden ser usados

### Validación de Usuario
- Verifica que el usuario no esté eliminado (soft delete)
- Valida credenciales de forma segura
- Maneja errores de autenticación

## Tests

### Feature Tests
- `AuthControllerTest`: Tests de endpoints API
- Validación de login/logout
- Verificación de eliminación de tokens anteriores
- Tests de autenticación con tokens

### Unit Tests
- `TokenTest`: Tests del Value Object Token
- Validación de formatos
- Tests de comparación

## Ejecutar Tests

```bash
# Tests de feature
php artisan test tests/Feature/Auth/

# Tests unitarios
php artisan test tests/Unit/ValueObjects/Auth/
```

## Uso con Frontend

### Login
```javascript
const response = await fetch('/api/v1/auth/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        email: 'user@example.com',
        password: 'password123'
    })
});

const data = await response.json();
const token = data.data.token;

// Guardar token
localStorage.setItem('auth_token', token);
```

### Requests Autenticados
```javascript
const token = localStorage.getItem('auth_token');

const response = await fetch('/api/v1/users', {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
    }
});
```

### Logout
```javascript
const token = localStorage.getItem('auth_token');

await fetch('/api/v1/auth/logout', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
    }
});

// Limpiar token local
localStorage.removeItem('auth_token');
```

## Próximos Pasos

1. Implementar middleware de autorización por roles
2. Agregar refresh token automático
3. Implementar rate limiting para login
4. Agregar autenticación de dos factores (2FA)
5. Implementar auditoría de sesiones 