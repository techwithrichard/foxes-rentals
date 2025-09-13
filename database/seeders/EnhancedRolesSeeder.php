<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EnhancedRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create enhanced roles
        $roles = [
            'super_admin' => 'Super Administrator - Full system access',
            'admin' => 'Administrator - Property management access',
            'landlord' => 'Landlord - Property owner access',
            'tenant' => 'Tenant - Rental access',
            'maintainer' => 'Maintainer - Property maintenance access',
            'accountant' => 'Accountant - Financial management access',
            'hr' => 'HR - Human resources access',
            'agent' => 'Agent - Property sales/rental agent access',
            'property_manager' => 'Property Manager - Property operations access',
            'finance_manager' => 'Finance Manager - Financial oversight access',
            'leasing_agent' => 'Leasing Agent - Lease management access',
            'sales_agent' => 'Sales Agent - Property sales access',
        ];

        foreach ($roles as $name => $description) {
            Role::updateOrCreate(
                ['name' => $name],
                ['name' => $name]
            );
        }

        // Create comprehensive permissions
        $permissions = [
            // System Management
            'view_admin_portal',
            'manage_system_settings',
            'view_system_logs',
            'manage_backups',
            'view_activity_logs',
            
            // User Management
            'manage_users',
            'create_user',
            'edit_user',
            'delete_user',
            'view_user',
            'manage_roles',
            'assign_roles',
            'view_user_activity',
            
            // Property Management - General
            'manage_properties',
            'create_property',
            'edit_property',
            'delete_property',
            'view_property',
            'manage_property_types',
            'manage_property_features',
            
            // Property Management - Rent
            'manage_rental_properties',
            'create_rental_property',
            'edit_rental_property',
            'delete_rental_property',
            'view_rental_property',
            'manage_rental_units',
            'create_rental_unit',
            'edit_rental_unit',
            'delete_rental_unit',
            'view_rental_unit',
            
            // Property Management - Sale
            'manage_sale_properties',
            'create_sale_property',
            'edit_sale_property',
            'delete_sale_property',
            'view_sale_property',
            'manage_sale_listings',
            'create_sale_listing',
            'edit_sale_listing',
            'delete_sale_listing',
            'view_sale_listing',
            
            // Property Management - Lease
            'manage_lease_properties',
            'create_lease_property',
            'edit_lease_property',
            'delete_lease_property',
            'view_lease_property',
            'manage_lease_agreements',
            'create_lease_agreement',
            'edit_lease_agreement',
            'delete_lease_agreement',
            'view_lease_agreement',
            
            // Tenant Management
            'manage_tenants',
            'create_tenant',
            'edit_tenant',
            'delete_tenant',
            'view_tenant',
            'view_archived_tenant',
            'recover_archived_tenant',
            'delete_archived_tenant',
            
            // Landlord Management
            'manage_landlords',
            'create_landlord',
            'edit_landlord',
            'delete_landlord',
            'view_landlord',
            
            // Lease Management
            'manage_leases',
            'create_lease',
            'edit_lease',
            'delete_lease',
            'view_lease',
            'view_lease_history',
            'delete_lease_history',
            'manage_lease_renewals',
            'create_lease_renewal',
            'edit_lease_renewal',
            'delete_lease_renewal',
            'view_lease_renewal',
            
            // Financial Management
            'manage_finances',
            'view_financial_reports',
            'create_payment',
            'edit_payment',
            'delete_payment',
            'view_payment',
            'manage_invoices',
            'create_invoice',
            'edit_invoice',
            'delete_invoice',
            'view_invoice',
            'manage_deposits',
            'edit_deposit',
            'delete_deposit',
            'view_deposit',
            'refund_deposit',
            'manage_overpayments',
            'delete_overpayment',
            'view_overpayment',
            'manage_expenses',
            'create_expense',
            'edit_expense',
            'delete_expense',
            'view_expense',
            
            // Maintenance Management
            'manage_maintenance',
            'create_maintenance_request',
            'edit_maintenance_request',
            'delete_maintenance_request',
            'view_maintenance_request',
            'assign_maintenance_task',
            'update_maintenance_status',
            'view_maintenance_history',
            
            // Reports
            'view_reports',
            'view_property_income_report',
            'view_company_income_report',
            'view_landlord_expenses_report',
            'view_company_expenses_report',
            'view_expiring_leases_report',
            'view_landlord_income_report',
            'view_outstanding_payments_report',
            'view_maintenance_reports',
            'view_occupancy_reports',
            'view_financial_summary',
            
            // Settings
            'view_settings',
            'manage_property_settings',
            'manage_financial_settings',
            'manage_system_settings',
            
            // Support
            'manage_support_tickets',
            'view_support_ticket',
            'edit_support_ticket',
            'delete_support_ticket',
            'create_support_ticket',
            
            // MPesa Integration
            'manage_mpesa_transactions',
            'view_mpesa_c2b_transactions',
            'view_mpesa_stk_transactions',
            'reconcile_mpesa_c2b_transactions',
            'process_mpesa_payments',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $this->assignRolePermissions();
    }

    private function assignRolePermissions()
    {
        // Super Admin - All permissions
        $superAdmin = Role::findByName('super_admin');
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - Most permissions except super admin specific
        $admin = Role::findByName('admin');
        $admin->givePermissionTo([
            'view_admin_portal',
            'manage_users', 'create_user', 'edit_user', 'delete_user', 'view_user',
            'manage_roles', 'assign_roles', 'view_user_activity',
            'manage_properties', 'create_property', 'edit_property', 'delete_property', 'view_property',
            'manage_rental_properties', 'create_rental_property', 'edit_rental_property', 'delete_rental_property', 'view_rental_property',
            'manage_sale_properties', 'create_sale_property', 'edit_sale_property', 'delete_sale_property', 'view_sale_property',
            'manage_lease_properties', 'create_lease_property', 'edit_lease_property', 'delete_lease_property', 'view_lease_property',
            'manage_tenants', 'create_tenant', 'edit_tenant', 'delete_tenant', 'view_tenant',
            'manage_landlords', 'create_landlord', 'edit_landlord', 'delete_landlord', 'view_landlord',
            'manage_leases', 'create_lease', 'edit_lease', 'delete_lease', 'view_lease',
            'manage_finances', 'view_financial_reports', 'create_payment', 'edit_payment', 'delete_payment', 'view_payment',
            'manage_invoices', 'create_invoice', 'edit_invoice', 'delete_invoice', 'view_invoice',
            'view_reports', 'view_property_income_report', 'view_company_income_report',
            'view_settings', 'manage_property_settings', 'manage_financial_settings',
            'manage_support_tickets', 'view_support_ticket', 'edit_support_ticket', 'delete_support_ticket',
            'manage_mpesa_transactions', 'view_mpesa_c2b_transactions', 'view_mpesa_stk_transactions',
        ]);

        // Property Manager
        $propertyManager = Role::findByName('property_manager');
        $propertyManager->givePermissionTo([
            'view_admin_portal',
            'manage_properties', 'create_property', 'edit_property', 'view_property',
            'manage_rental_properties', 'create_rental_property', 'edit_rental_property', 'view_rental_property',
            'manage_sale_properties', 'create_sale_property', 'edit_sale_property', 'view_sale_property',
            'manage_lease_properties', 'create_lease_property', 'edit_lease_property', 'view_lease_property',
            'manage_tenants', 'create_tenant', 'edit_tenant', 'view_tenant',
            'manage_landlords', 'view_landlord',
            'manage_leases', 'create_lease', 'edit_lease', 'view_lease',
            'view_reports', 'view_property_income_report', 'view_occupancy_reports',
            'manage_support_tickets', 'view_support_ticket', 'create_support_ticket',
        ]);

        // Finance Manager
        $financeManager = Role::findByName('finance_manager');
        $financeManager->givePermissionTo([
            'view_admin_portal',
            'manage_finances', 'view_financial_reports', 'create_payment', 'edit_payment', 'view_payment',
            'manage_invoices', 'create_invoice', 'edit_invoice', 'view_invoice',
            'manage_deposits', 'edit_deposit', 'view_deposit', 'refund_deposit',
            'manage_overpayments', 'view_overpayment',
            'manage_expenses', 'create_expense', 'edit_expense', 'view_expense',
            'view_reports', 'view_financial_summary', 'view_company_income_report',
            'manage_financial_settings',
            'manage_mpesa_transactions', 'view_mpesa_c2b_transactions', 'view_mpesa_stk_transactions',
        ]);

        // Accountant
        $accountant = Role::findByName('accountant');
        $accountant->givePermissionTo([
            'view_admin_portal',
            'view_financial_reports', 'view_payment',
            'view_invoice', 'create_invoice', 'edit_invoice',
            'view_deposit', 'view_overpayment',
            'view_expense', 'create_expense', 'edit_expense',
            'view_reports', 'view_financial_summary',
            'view_mpesa_c2b_transactions', 'view_mpesa_stk_transactions',
        ]);

        // Leasing Agent
        $leasingAgent = Role::findByName('leasing_agent');
        $leasingAgent->givePermissionTo([
            'view_admin_portal',
            'view_rental_property', 'view_lease_property',
            'manage_tenants', 'create_tenant', 'edit_tenant', 'view_tenant',
            'manage_leases', 'create_lease', 'edit_lease', 'view_lease',
            'manage_lease_agreements', 'create_lease_agreement', 'edit_lease_agreement', 'view_lease_agreement',
            'view_reports', 'view_occupancy_reports',
        ]);

        // Sales Agent
        $salesAgent = Role::findByName('sales_agent');
        $salesAgent->givePermissionTo([
            'view_admin_portal',
            'view_sale_property', 'create_sale_property', 'edit_sale_property',
            'manage_sale_listings', 'create_sale_listing', 'edit_sale_listing', 'view_sale_listing',
            'view_landlord',
            'view_reports', 'view_property_income_report',
        ]);

        // Maintainer
        $maintainer = Role::findByName('maintainer');
        $maintainer->givePermissionTo([
            'view_admin_portal',
            'manage_maintenance', 'create_maintenance_request', 'edit_maintenance_request', 'view_maintenance_request',
            'assign_maintenance_task', 'update_maintenance_status', 'view_maintenance_history',
            'view_property', 'view_rental_property',
        ]);

        // HR
        $hr = Role::findByName('hr');
        $hr->givePermissionTo([
            'view_admin_portal',
            'manage_users', 'create_user', 'edit_user', 'view_user',
            'view_user_activity',
            'manage_support_tickets', 'view_support_ticket', 'create_support_ticket',
        ]);

        // Landlord
        $landlord = Role::findByName('landlord');
        $landlord->givePermissionTo([
            'view_property', 'view_rental_property', 'view_sale_property', 'view_lease_property',
            'view_tenant', 'view_lease',
            'view_payment', 'view_invoice',
            'view_reports', 'view_landlord_income_report',
        ]);

        // Tenant
        $tenant = Role::findByName('tenant');
        $tenant->givePermissionTo([
            'view_rental_property',
            'view_lease',
            'view_payment', 'create_payment',
            'view_invoice',
            'create_maintenance_request', 'view_maintenance_request',
        ]);
    }
}
