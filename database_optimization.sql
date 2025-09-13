-- Foxes Rental Management System - Database Optimization Script
-- This script adds indexes and optimizes database performance

-- Enable slow query log for monitoring
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
SET GLOBAL log_queries_not_using_indexes = 'ON';

-- Add indexes for frequently queried columns
-- Users table optimizations
ALTER TABLE users ADD INDEX idx_users_email (email);
ALTER TABLE users ADD INDEX idx_users_created_at (created_at);
ALTER TABLE users ADD INDEX idx_users_updated_at (updated_at);

-- Properties table optimizations
ALTER TABLE properties ADD INDEX idx_properties_user_id (user_id);
ALTER TABLE properties ADD INDEX idx_properties_status (status);
ALTER TABLE properties ADD INDEX idx_properties_created_at (created_at);
ALTER TABLE properties ADD INDEX idx_properties_location (location);

-- Tenants table optimizations (if exists)
-- ALTER TABLE tenants ADD INDEX idx_tenants_property_id (property_id);
-- ALTER TABLE tenants ADD INDEX idx_tenants_status (status);
-- ALTER TABLE tenants ADD INDEX idx_tenants_created_at (created_at);

-- Payments table optimizations
ALTER TABLE payments ADD INDEX idx_payments_user_id (user_id);
ALTER TABLE payments ADD INDEX idx_payments_property_id (property_id);
ALTER TABLE payments ADD INDEX idx_payments_status (status);
ALTER TABLE payments ADD INDEX idx_payments_created_at (created_at);
ALTER TABLE payments ADD INDEX idx_payments_amount (amount);

-- Invoices table optimizations
ALTER TABLE invoices ADD INDEX idx_invoices_user_id (user_id);
ALTER TABLE invoices ADD INDEX idx_invoices_property_id (property_id);
ALTER TABLE invoices ADD INDEX idx_invoices_status (status);
ALTER TABLE invoices ADD INDEX idx_invoices_due_date (due_date);
ALTER TABLE invoices ADD INDEX idx_invoices_created_at (created_at);

-- Leases table optimizations
ALTER TABLE leases ADD INDEX idx_leases_property_id (property_id);
ALTER TABLE leases ADD INDEX idx_leases_tenant_id (tenant_id);
ALTER TABLE leases ADD INDEX idx_leases_status (status);
ALTER TABLE leases ADD INDEX idx_leases_start_date (start_date);
ALTER TABLE leases ADD INDEX idx_leases_end_date (end_date);

-- Houses table optimizations
ALTER TABLE houses ADD INDEX idx_houses_property_id (property_id);
ALTER TABLE houses ADD INDEX idx_houses_status (status);
ALTER TABLE houses ADD INDEX idx_houses_created_at (created_at);

-- Landlords table optimizations
ALTER TABLE landlords ADD INDEX idx_landlords_user_id (user_id);
ALTER TABLE landlords ADD INDEX idx_landlords_status (status);
ALTER TABLE landlords ADD INDEX idx_landlords_created_at (created_at);

-- Activity log optimizations
ALTER TABLE activity_log ADD INDEX idx_activity_log_log_name (log_name);
ALTER TABLE activity_log ADD INDEX idx_activity_log_event (event);
ALTER TABLE activity_log ADD INDEX idx_activity_log_created_at (created_at);
ALTER TABLE activity_log ADD INDEX idx_activity_log_subject_id (subject_id);
ALTER TABLE activity_log ADD INDEX idx_activity_log_causer_id (causer_id);

-- Permissions optimizations
ALTER TABLE permissions ADD INDEX idx_permissions_name (name);
ALTER TABLE permissions ADD INDEX idx_permissions_guard_name (guard_name);

-- Roles optimizations
ALTER TABLE roles ADD INDEX idx_roles_name (name);
ALTER TABLE roles ADD INDEX idx_roles_guard_name (guard_name);

-- Model has permissions optimizations
ALTER TABLE model_has_permissions ADD INDEX idx_model_has_permissions_model_id (model_id);
ALTER TABLE model_has_permissions ADD INDEX idx_model_has_permissions_permission_id (permission_id);

-- Model has roles optimizations
ALTER TABLE model_has_roles ADD INDEX idx_model_has_roles_model_id (model_id);
ALTER TABLE model_has_roles ADD INDEX idx_model_has_roles_role_id (role_id);

-- Role has permissions optimizations
ALTER TABLE role_has_permissions ADD INDEX idx_role_has_permissions_role_id (role_id);
ALTER TABLE role_has_permissions ADD INDEX idx_role_has_permissions_permission_id (permission_id);

-- Optimize table statistics
ANALYZE TABLE users;
ANALYZE TABLE properties;
ANALYZE TABLE payments;
ANALYZE TABLE invoices;
ANALYZE TABLE leases;
ANALYZE TABLE houses;
ANALYZE TABLE landlords;
ANALYZE TABLE activity_log;
ANALYZE TABLE permissions;
ANALYZE TABLE roles;
ANALYZE TABLE model_has_permissions;
ANALYZE TABLE model_has_roles;
ANALYZE TABLE role_has_permissions;

-- Show index usage statistics
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    CARDINALITY,
    INDEX_TYPE
FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY TABLE_NAME, INDEX_NAME;

-- Show table sizes
SELECT 
    TABLE_NAME,
    ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) AS 'Size (MB)',
    TABLE_ROWS
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE()
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

