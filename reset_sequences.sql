-- Script para resetear las secuencias de auto-incremento en todas las tablas
-- Esto asegurará que los IDs empiecen desde 1 y sean consecutivos

-- Resetear secuencia de la tabla users
SELECT setval('users_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM users), false);

-- Resetear secuencia de la tabla companies
SELECT setval('companies_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM companies), false);

-- Resetear secuencia de la tabla plans
SELECT setval('plans_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM plans), false);

-- Resetear secuencia de la tabla subscriptions
SELECT setval('subscriptions_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM subscriptions), false);

-- Resetear secuencia de la tabla personal_access_tokens
SELECT setval('personal_access_tokens_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM personal_access_tokens), false);

-- Resetear secuencia de la tabla roles
SELECT setval('roles_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM roles), false);

-- Resetear secuencia de la tabla permissions
SELECT setval('permissions_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM permissions), false);

-- Resetear secuencia de la tabla model_has_roles
SELECT setval('model_has_roles_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM model_has_roles), false);

-- Resetear secuencia de la tabla model_has_permissions
SELECT setval('model_has_permissions_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM model_has_permissions), false);

-- Resetear secuencia de la tabla role_has_permissions
SELECT setval('role_has_permissions_id_seq', (SELECT COALESCE(MAX(id), 0) + 1 FROM role_has_permissions), false);

-- Verificar las secuencias actuales
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