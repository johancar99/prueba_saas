# Módulo de Planes

## Descripción

El módulo de Planes implementa un CRUD completo siguiendo los principios de Clean Architecture y Domain Driven Design (DDD). Este módulo permite gestionar planes de suscripción con sus características, precios y límites de usuarios.

## Arquitectura

### Estructura de Carpetas

```
app/
├── Domain/Plan/
│   ├── Plan.php                    # Entidad del dominio
│   └── PlanRepositoryInterface.php # Interfaz del repositorio
├── ValueObjects/Plan/
│   ├── PlanId.php                  # ID del plan
│   ├── PlanName.php                # Nombre del plan
│   ├── MonthlyPrice.php            # Precio mensual
│   ├── UserLimit.php               # Límite de usuarios
│   └── Features.php                # Características del plan
├── DTOs/Plan/
│   ├── CreatePlanDTO.php           # DTO para crear planes
│   ├── UpdatePlanDTO.php           # DTO para actualizar planes
│   └── PlanResponseDTO.php         # DTO para respuestas
├── UseCases/Plan/
│   ├── CreatePlanUseCase.php       # Caso de uso: Crear plan
│   ├── GetPlanUseCase.php          # Caso de uso: Obtener plan
│   ├── ListPlansUseCase.php        # Caso de uso: Listar planes
│   ├── UpdatePlanUseCase.php       # Caso de uso: Actualizar plan
│   ├── DeletePlanUseCase.php       # Caso de uso: Eliminar plan
│   └── RestorePlanUseCase.php      # Caso de uso: Restaurar plan
├── Infrastructure/Plan/
│   └── PlanRepository.php          # Implementación del repositorio
├── Http/Controllers/Api/V1/Plan/
│   └── PlanController.php          # Controlador API
├── Http/Requests/Plan/
│   ├── CreatePlanRequest.php       # Validación para crear
│   └── UpdatePlanRequest.php       # Validación para actualizar
├── Models/
│   └── Plan.php                    # Modelo Eloquent
└── Providers/
    └── PlanServiceProvider.php     # Service Provider
```

## Campos del Plan

- **name**: Nombre del plan (string, 2-255 caracteres)
- **monthly_price**: Precio mensual (decimal, 0-999,999.99)
- **user_limit**: Límite de usuarios (integer, 1-1,000,000, -1 para ilimitado)
- **features**: Características del plan (array de strings)
- **is_active**: Estado activo del plan (boolean)
- **created_at**: Fecha de creación
- **updated_at**: Fecha de actualización
- **deleted_at**: Fecha de eliminación (soft delete)

## Endpoints API

### Base URL
```
/api/v1/plans
```

### Endpoints Disponibles

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/v1/plans` | Listar todos los planes (con paginación) |
| POST | `/api/v1/plans` | Crear un nuevo plan |
| GET | `/api/v1/plans/{id}` | Obtener un plan específico |
| PUT | `/api/v1/plans/{id}` | Actualizar un plan |
| DELETE | `/api/v1/plans/{id}` | Eliminar un plan (soft delete) |
| PATCH | `/api/v1/plans/{id}/restore` | Restaurar un plan eliminado |

### Parámetros de Consulta

#### Listar Planes
```
GET /api/v1/plans?per_page=15&page=1
```

- `per_page`: Número de elementos por página (default: 15)
- `page`: Número de página (default: 1)

### Ejemplos de Uso

#### Crear un Plan
```bash
curl -X POST /api/v1/plans \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Basic Plan",
    "monthly_price": 29.99,
    "user_limit": 10,
    "features": [
      "User Management",
      "Basic Analytics",
      "Email Support"
    ]
  }'
```

**Respuesta:**
```json
{
  "message": "Plan created successfully",
  "data": {
    "id": 1,
    "name": "Basic Plan",
    "monthly_price": 29.99,
    "annual_price": 359.88,
    "user_limit": 10,
    "user_limit_display": "10",
    "features": [
      "User Management",
      "Basic Analytics",
      "Email Support"
    ],
    "features_count": 3,
    "is_active": true,
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  }
}
```

#### Actualizar un Plan
```bash
curl -X PUT /api/v1/plans/1 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Basic Plan",
    "monthly_price": 39.99,
    "user_limit": 15
  }'
```

#### Listar Planes
```bash
curl -X GET /api/v1/plans?per_page=10&page=1
```

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Basic Plan",
      "monthly_price": 29.99,
      "annual_price": 359.88,
      "user_limit": 10,
      "user_limit_display": "10",
      "features": ["User Management", "Basic Analytics"],
      "features_count": 2,
      "is_active": true,
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  }
}
```

## Validaciones

### Crear Plan
- `name`: Requerido, string, 2-255 caracteres
- `monthly_price`: Requerido, numérico, 0-999,999.99
- `user_limit`: Requerido, entero, 1-1,000,000
- `features`: Requerido, array, mínimo 1 elemento
- `features.*`: Requerido, string, 1-255 caracteres

### Actualizar Plan
- `name`: Opcional, string, 2-255 caracteres
- `monthly_price`: Opcional, numérico, 0-999,999.99
- `user_limit`: Opcional, entero, 1-1,000,000
- `features`: Opcional, array, mínimo 1 elemento
- `features.*`: Requerido cuando features está presente, string, 1-255 caracteres

## Características Especiales

### Cálculos Automáticos
- **annual_price**: Calculado automáticamente (monthly_price × 12)
- **user_limit_display**: Muestra "Unlimited" para límite -1
- **features_count**: Número total de características

### Soft Deletes
- Los planes se eliminan con soft delete
- Endpoint `/restore` para recuperar planes eliminados
- Los planes eliminados no aparecen en listados normales

### Límites de Usuarios
- Valores positivos: límite específico de usuarios
- Valor -1: límite ilimitado de usuarios
- Métodos de negocio para verificar capacidad

### Precios
- Validación de rangos (0-999,999.99)
- Redondeo automático a 2 decimales
- Operaciones matemáticas seguras (suma, resta, multiplicación)

## Tests

### Tests de Feature
- `tests/Feature/Plan/PlanControllerTest.php`
- Cobertura completa de endpoints CRUD
- Validación de respuestas y estados HTTP
- Verificación de soft deletes

### Tests Unitarios
- `tests/Unit/ValueObjects/Plan/MonthlyPriceTest.php`
- Validación de Value Objects
- Pruebas de lógica de negocio

### Factory
- `database/factories/PlanFactory.php`
- Datos de prueba realistas
- Estados predefinidos (basic, professional, enterprise)

## Configuración

### Service Provider
El `PlanServiceProvider` registra las dependencias:
- `PlanRepositoryInterface` → `PlanRepository`

### Migración
```bash
php artisan migrate
```

### Tests
```bash
php artisan test --filter=Plan
```

## Casos de Uso

### Crear Plan
1. Validar datos de entrada
2. Crear Value Objects
3. Crear entidad Plan
4. Persistir en repositorio
5. Retornar DTO de respuesta

### Actualizar Plan
1. Buscar plan por ID
2. Validar datos de entrada
3. Aplicar cambios parciales
4. Persistir cambios
5. Retornar plan actualizado

### Eliminar Plan
1. Buscar plan por ID
2. Verificar que no esté eliminado
3. Aplicar soft delete
4. Persistir cambios

### Restaurar Plan
1. Buscar plan por ID
2. Verificar que esté eliminado
3. Restaurar plan
4. Persistir cambios

## Ventajas de la Arquitectura

### Separación de Responsabilidades
- **Dominio**: Lógica de negocio pura
- **Aplicación**: Casos de uso y orquestación
- **Infraestructura**: Persistencia y servicios externos
- **Presentación**: Controllers y validación

### Testabilidad
- Value Objects con validación robusta
- Casos de uso aislados y testables
- Repositorio abstracto para mocking

### Mantenibilidad
- Código organizado por capas
- Dependencias inyectadas
- Validaciones centralizadas

### Escalabilidad
- Fácil agregar nuevos casos de uso
- Repositorio intercambiable
- Value Objects reutilizables

## Próximas Mejoras

1. **Búsqueda Avanzada**: Filtros por precio, características, estado
2. **Ordenamiento**: Por precio, nombre, fecha de creación
3. **Exportación**: CSV, JSON, PDF
4. **Auditoría**: Log de cambios en planes
5. **Validaciones de Negocio**: Verificar conflictos de nombres
6. **Cache**: Cache de planes activos
7. **Eventos**: Eventos de dominio para integraciones 