# Proyecto SaaS - Laravel API

Un sistema SaaS completo construido con Laravel 12, implementando Clean Architecture y Domain Driven Design (DDD).

## 🚀 Características

- ✅ **Arquitectura Limpia**: Separación clara de capas (Domain, Application, Infrastructure)
- ✅ **DDD**: Value Objects, DTOs, UseCases, Repositorios
- ✅ **Autenticación**: Laravel Sanctum con tokens de 24 horas
- ✅ **Autorización**: Sistema de roles y permisos con Spatie
- ✅ **API REST**: Endpoints versionados bajo `/api/v1/`
- ✅ **Soft Deletes**: Soporte para borrado lógico
- ✅ **Paginación**: Listados paginados
- ✅ **Validación**: Form Requests para validación de entrada
- ✅ **Tests**: Tests unitarios y de feature
- ✅ **Documentación**: Swagger/OpenAPI integrado
- ✅ **Módulos**: Usuarios, Planes, Empresas, Autenticación

## 📋 Requisitos del Sistema

### Software Requerido
- **PHP**: 8.2 o superior
- **Composer**: 2.0 o superior
- **Node.js**: 18.0 o superior
- **NPM**: 9.0 o superior
- **Base de Datos**: MySQL 8.0+, PostgreSQL 13+, o SQLite 3

### Extensiones PHP Requeridas
```bash
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
```

## 🛠️ Instalación

### 1. Clonar el Repositorio
```bash
git clone <url-del-repositorio>
cd proyecto_saas
```

### 2. Instalar Dependencias PHP
```bash
composer install
```

### 3. Instalar Dependencias Node.js
```bash
npm install
```

### 4. Configurar Variables de Entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 5. Configurar Base de Datos

#### Opción A: SQLite (Desarrollo)
```bash
# Crear archivo de base de datos
touch database/database.sqlite

# Configurar .env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/absoluta/a/proyecto_saas/database/database.sqlite
```

#### Opción B: MySQL/PostgreSQL (Producción)
```bash
# Configurar .env con credenciales de tu base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=proyecto_saas
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

### 6. Ejecutar Migraciones
```bash
php artisan migrate
```

### 7. Ejecutar Seeders
```bash
php artisan db:seed
```

### 8. Publicar Assets (Opcional)
```bash
php artisan vendor:publish --tag=laravel-assets
```

### 9. Configurar Almacenamiento
```bash
# Crear enlace simbólico para storage
php artisan storage:link
```

### 10. Compilar Assets (Desarrollo)
```bash
npm run dev
```

### 11. Compilar Assets (Producción)
```bash
npm run build
```

## 🚀 Ejecutar el Proyecto

### Servidor de Desarrollo
```bash
# Iniciar servidor Laravel
php artisan serve

# En otra terminal, compilar assets en modo desarrollo
npm run dev
```

### Comando de Desarrollo Completo
```bash
# Ejecutar servidor, cola, logs y Vite simultáneamente
composer run dev
```

## 📚 Módulos del Sistema

### 🔐 Módulo de Autenticación
- **Laravel Sanctum**: Tokens de autenticación
- **Expiración**: 24 horas configurable
- **Eliminación automática**: Tokens anteriores se eliminan al hacer login
- **Endpoints**: Login, Logout, Refresh, Me

**Documentación completa**: [README_AUTH_MODULE.md](README_AUTH_MODULE.md)

### 👥 Módulo de Usuarios
- **CRUD completo**: Crear, leer, actualizar, eliminar usuarios
- **Soft deletes**: Soporte para borrado lógico
- **Paginación**: Listados paginados
- **Value Objects**: Email, Password, Name, UserId
- **Roles**: Sistema de roles y permisos

**Documentación completa**: [README_USER_MODULE.md](README_USER_MODULE.md)

### 📦 Módulo de Planes
- **Gestión de planes**: Crear y gestionar planes de suscripción
- **Características**: Precios, límites de usuarios, características
- **Cálculos automáticos**: Precio anual, conteo de características
- **Soft deletes**: Soporte para borrado lógico

**Documentación completa**: [README_PLAN_MODULE.md](README_PLAN_MODULE.md)

### 🏢 Módulo de Empresas
- **Gestión de empresas**: CRUD de empresas
- **Suscripciones**: Sistema de suscripciones a planes
- **Cambio de planes**: Funcionalidad para cambiar planes
- **Eventos**: Eventos de dominio para acciones importantes

## 🔌 API Endpoints

### Autenticación
```
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/logout-all
POST   /api/v1/auth/refresh
GET    /api/v1/auth/me
```

### Usuarios
```
GET    /api/v1/users
POST   /api/v1/users
GET    /api/v1/users/{id}
PUT    /api/v1/users/{id}
DELETE /api/v1/users/{id}
PATCH  /api/v1/users/{id}/restore
```

### Planes
```
GET    /api/v1/plans
POST   /api/v1/plans
GET    /api/v1/plans/{id}
PUT    /api/v1/plans/{id}
DELETE /api/v1/plans/{id}
PATCH  /api/v1/plans/{id}/restore
```

### Empresas
```
GET    /api/v1/companies
POST   /api/v1/companies
GET    /api/v1/companies/{id}
PUT    /api/v1/companies/{id}
DELETE /api/v1/companies/{id}
POST   /api/v1/companies/{id}/change-plan
```

## 🔐 Autenticación y Autorización

### Tokens de Acceso
```bash
# Login
curl -X POST /api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'
```

### Uso de Tokens
```bash
# Ejemplo de uso con token
curl -X GET /api/v1/users \
  -H "Authorization: Bearer {tu_token}"
```

### Roles del Sistema
- **super-admin**: Acceso completo a todos los módulos
- **admin**: Gestión de usuarios y empresas
- **user**: Acceso básico a funcionalidades

## 🧪 Testing

### Ejecutar Todos los Tests
```bash
php artisan test
```

### Tests por Módulo
```bash
# Tests de autenticación
php artisan test tests/Feature/Auth/

# Tests de usuarios
php artisan test tests/Feature/User/

# Tests de planes
php artisan test tests/Feature/Plan/

# Tests de empresas
php artisan test tests/Feature/Company/

# Tests unitarios
php artisan test tests/Unit/
```

### Tests con Cobertura
```bash
php artisan test --coverage
```

## 📊 Base de Datos

### Estructura Principal
- **users**: Usuarios del sistema
- **plans**: Planes de suscripción
- **companies**: Empresas
- **subscriptions**: Suscripciones de empresas a planes
- **permissions**: Permisos del sistema
- **roles**: Roles de usuarios
- **model_has_roles**: Relación usuarios-roles
- **model_has_permissions**: Relación usuarios-permisos

### Migraciones
```bash
# Ejecutar migraciones
php artisan migrate

# Revertir migraciones
php artisan migrate:rollback

# Refrescar base de datos
php artisan migrate:fresh --seed
```

### Seeders
```bash
# Ejecutar todos los seeders
php artisan db:seed

# Ejecutar seeder específico
php artisan db:seed --class=RoleAndPermissionSeeder
```

## 🔧 Comandos Artisan

### Comandos Disponibles
```bash
# Resetear secuencias de ID (PostgreSQL)
php artisan reset:sequences

# Limpiar cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar aplicación
php artisan optimize
php artisan config:cache
php artisan route:cache
```

## 📖 Documentación API

### Swagger/OpenAPI
La documentación de la API está disponible en:
```
http://localhost:8000/api/documentation
```

### Generar Documentación
```bash
# Generar documentación
php artisan l5-swagger:generate
```

## 🚀 Despliegue

### Configuración de Producción
```bash
# Configurar variables de entorno
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Configurar base de datos de producción
DB_CONNECTION=mysql
DB_HOST=tu-host
DB_DATABASE=tu-base-de-datos
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-password

# Configurar cache y sesiones
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Optimizaciones de Producción
```bash
# Compilar assets
npm run build

# Cachear configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimizar autoloader
composer install --optimize-autoloader --no-dev
```

## 🛠️ Desarrollo

### Estructura del Proyecto
```
app/
├── Application/          # Casos de uso y DTOs
├── Domain/              # Entidades y interfaces
├── Infrastructure/      # Implementaciones concretas
├── Http/               # Controladores y requests
├── Models/             # Modelos Eloquent
├── Providers/          # Service providers
└── ValueObjects/       # Value objects del dominio
```

### Convenciones de Código
- **PSR-12**: Estándar de codificación PHP
- **Laravel Pint**: Formateo automático de código
- **Clean Architecture**: Separación de capas
- **DDD**: Domain Driven Design

### Comandos de Desarrollo
```bash
# Formatear código
./vendor/bin/pint

# Analizar código
./vendor/bin/pint --test

# Ejecutar en modo desarrollo
composer run dev
```

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 🆘 Soporte

Si tienes problemas o preguntas:

1. Revisa la documentación de cada módulo
2. Ejecuta los tests para verificar la instalación
3. Verifica las variables de entorno
4. Revisa los logs en `storage/logs/`

## 📚 Recursos Adicionales

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)
