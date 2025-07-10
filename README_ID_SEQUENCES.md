# Solución para IDs Consecutivos

## Problema
Los IDs de las tablas están usando números aleatorios en lugar de ser consecutivos empezando desde 1.

## Causa
El problema se originó en el seeder `RoleAndPermissionSeeder.php` donde se estaba forzando el ID del super admin a 1, lo que puede causar problemas con la secuencia de auto-incremento.

## Soluciones Aplicadas

### 1. Corregido el Seeder
- Removido el `'id' => 1` del `RoleAndPermissionSeeder.php`
- Ahora el super admin se creará con ID automático

### 2. Comando Artisan (Recomendado)
Ejecuta el comando para resetear todas las secuencias:

```bash
php artisan db:reset-sequences
```

### 3. Script SQL Manual
Si prefieres usar SQL directamente, ejecuta el archivo `reset_sequences.sql` en tu base de datos.

### 4. Migración Completa (Opción Nuclear)
Si quieres empezar desde cero:

```bash
php artisan migrate:fresh --seed
```

## Verificación
Después de ejecutar cualquiera de las soluciones, verifica que:

1. Los nuevos registros tengan IDs consecutivos
2. El super admin tenga ID = 1
3. Las secuencias empiecen desde el valor correcto

## Comando de Verificación
Puedes verificar las secuencias actuales ejecutando:

```sql
SELECT 
    'users' as table_name,
    currval('users_id_seq') as current_value
UNION ALL
SELECT 
    'companies' as table_name,
    currval('companies_id_seq') as current_value
UNION ALL
SELECT 
    'plans' as table_name,
    currval('plans_id_seq') as current_value
UNION ALL
SELECT 
    'subscriptions' as table_name,
    currval('subscriptions_id_seq') as current_value;
```

## Notas Importantes
- El comando `db:reset-sequences` es seguro y no afecta los datos existentes
- Solo resetea las secuencias de auto-incremento para que el próximo ID sea consecutivo
- Si usas `migrate:fresh`, perderás todos los datos existentes 