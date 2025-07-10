# Módulo de Usuarios - Arquitectura Limpia y DDD

Este módulo implementa un CRUD completo de usuarios siguiendo los principios de Domain Driven Design (DDD) y Clean Architecture.

## Estructura del Módulo

```
app/
├── Domain/
│   └── User/
│       ├── User.php                    # Entidad de dominio
│       └── UserRepositoryInterface.php # Interfaz del repositorio
├── Application/
│   └── User/                          # Casos de uso (UseCases)
├── Infrastructure/
│   └── User/
│       └── UserRepository.php         # Implementación Eloquent
├── UseCases/
│   └── User/
│       ├── CreateUserUseCase.php
│       ├── GetUserUseCase.php
│       ├── ListUsersUseCase.php
│       ├── UpdateUserUseCase.php
│       ├── DeleteUserUseCase.php
│       └── RestoreUserUseCase.php
├── DTOs/
│   └── User/
│       ├── CreateUserDTO.php
│       ├── UpdateUserDTO.php
│       └── UserResponseDTO.php
├── ValueObjects/
│   └── User/
│       ├── UserId.php
│       ├── Name.php
│       ├── Email.php
│       └── Password.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── V1/
│   │           └── UserController.php
│   └── Requests/
│       └── User/
│           ├── CreateUserRequest.php
│           └── UpdateUserRequest.php
└── Providers/
    └── UserServiceProvider.php
```

## Características

- ✅ **Arquitectura Limpia**: Separación clara de capas (Domain, Application, Infrastructure)
- ✅ **DDD**: Entidades de dominio, Value Objects, Repositorios
- ✅ **Soft Deletes**: Soporte para borrado lógico
- ✅ **Paginación**: Listado paginado de usuarios
- ✅ **Validación**: Form Requests para validación de entrada
- ✅ **DTOs**: Data Transfer Objects para entrada/salida
- ✅ **Value Objects**: Email, Password, Name, UserId
- ✅ **Testeable**: Tests unitarios y de feature
- ✅ **API Versionada**: Rutas bajo `/api/v1/`

## Endpoints

### Listar Usuarios
```
GET /api/v1/users
GET /api/v1/users?page=1&per_page=15
```

### Crear Usuario
```
POST /api/v1/users
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
}
```

### Obtener Usuario
```
GET /api/v1/users/{id}
```

### Actualizar Usuario
```
PUT /api/v1/users/{id}
Content-Type: application/json

{
    "name": "Updated Name",
    "email": "updated@example.com",
    "password": "newpassword123"
}
```

### Eliminar Usuario (Soft Delete)
```
DELETE /api/v1/users/{id}
```

### Restaurar Usuario
```
PATCH /api/v1/users/{id}/restore
```

## Value Objects

### Email
- Validación de formato
- Normalización a minúsculas
- Métodos para obtener dominio y parte local

### Password
- Validación de longitud mínima (8 caracteres)
- Hashing automático
- Verificación de contraseñas
- Detección de rehash necesario

### Name
- Validación de longitud (2-255 caracteres)
- Trim automático

### UserId
- Validación de ID positivo
- Generación automática

## Casos de Uso

1. **CreateUserUseCase**: Crear usuario con validación de email único
2. **GetUserUseCase**: Obtener usuario por ID
3. **ListUsersUseCase**: Listar usuarios con paginación
4. **UpdateUserUseCase**: Actualizar usuario con validaciones
5. **DeleteUserUseCase**: Eliminar usuario (soft delete)
6. **RestoreUserUseCase**: Restaurar usuario eliminado

## Tests

### Feature Tests
- `UserControllerTest`: Tests de endpoints API
- Validación de respuestas JSON
- Verificación de soft deletes
- Tests de paginación

### Unit Tests
- `EmailTest`: Tests del Value Object Email
- Validación de formatos
- Tests de normalización

## Configuración

El módulo se registra automáticamente a través de `UserServiceProvider` que:
- Registra el binding del repositorio
- Permite inyección de dependencias

## Ejecutar Tests

```bash
# Tests de feature
php artisan test tests/Feature/User/

# Tests unitarios
php artisan test tests/Unit/ValueObjects/User/
```

## Próximos Pasos

1. Implementar autenticación con Laravel Sanctum
2. Agregar middleware de autorización
3. Implementar búsqueda avanzada
4. Agregar filtros por estado (activo/eliminado)
5. Implementar exportación de datos 