<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::query()->delete();

        //create permissions

        Permission::create(['name' => 'view_admin_portal']);

        Permission::create(['name' => 'create tenant']);
        Permission::create(['name' => 'edit tenant']);
        Permission::create(['name' => 'delete tenant']);
        Permission::create(['name' => 'view tenant']);

        //archived tenant. View,recover,delete
        Permission::create(['name' => 'view archived tenant']);
        Permission::create(['name' => 'recover archived tenant']);
        Permission::create(['name' => 'delete archived tenant']);

        Permission::create(['name' => 'create landlord']);
        Permission::create(['name' => 'edit landlord']);
        Permission::create(['name' => 'delete landlord']);
        Permission::create(['name' => 'view landlord']);

        Permission::create(['name' => 'create property']);
        Permission::create(['name' => 'edit property']);
        Permission::create(['name' => 'delete property']);
        Permission::create(['name' => 'view property']);


        Permission::create(['name' => 'create house']);
        Permission::create(['name' => 'edit house']);
        Permission::create(['name' => 'delete house']);
        Permission::create(['name' => 'view house']);

        //Lease
        Permission::create(['name' => 'create lease']);
        Permission::create(['name' => 'edit lease']);
        Permission::create(['name' => 'delete lease']);
        Permission::create(['name' => 'view lease']);
        //Lease history,view,delete
        Permission::create(['name' => 'view lease history']);
        Permission::create(['name' => 'delete lease history']);


        //Payment
        Permission::create(['name' => 'create payment']);
        Permission::create(['name' => 'edit payment']);
        Permission::create(['name' => 'delete payment']);
        Permission::create(['name' => 'view payment']);

        //Invoice
        Permission::create(['name' => 'create invoice']);
        Permission::create(['name' => 'edit invoice']);
        Permission::create(['name' => 'delete invoice']);
        Permission::create(['name' => 'view invoice']);

        //view reports
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'view property income report']);
        Permission::create(['name' => 'view company income report']);
        Permission::create(['name' => 'view landlord expenses report']);
        Permission::create(['name' => 'view company expenses report']);
        Permission::create(['name' => 'view expiring leases report']);
        Permission::create(['name' => 'view landlord income report']);
        Permission::create(['name' => 'view outstanding payments report']);



        //view settings
        Permission::create(['name' => 'view settings']);

        //manage users
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'manage roles']);

        //CRUD landlord vouchers
        Permission::create(['name' => 'create landlord voucher']);
        Permission::create(['name' => 'edit landlord voucher']);
        Permission::create(['name' => 'delete landlord voucher']);
        Permission::create(['name' => 'view landlord voucher']);

        //CRUD custom invoices
        Permission::create(['name' => 'create custom invoice']);
        Permission::create(['name' => 'edit custom invoice']);
        Permission::create(['name' => 'delete custom invoice']);
        Permission::create(['name' => 'view custom invoice']);

        //CRUD expenses
        Permission::create(['name' => 'create expense']);
        Permission::create(['name' => 'edit expense']);
        Permission::create(['name' => 'delete expense']);
        Permission::create(['name' => 'view expense']);

        //CRUD overpayments
        Permission::create(['name' => 'delete overpayment']);
        Permission::create(['name' => 'view overpayment']);

        //Deposits
        Permission::create(['name' => 'edit deposit']);
        Permission::create(['name' => 'delete deposit']);
        Permission::create(['name' => 'view deposit']);
        Permission::create(['name' => 'refund deposit']);

        //Landlord Remittance CRUD
        Permission::create(['name' => 'view landlord remittance']);
        Permission::create(['name' => 'create landlord remittance']);
        Permission::create(['name' => 'edit landlord remittance']);
        Permission::create(['name' => 'delete landlord remittance']);

        //Support Tickets. View, create, edit, delete
        Permission::create(['name' => 'view support ticket']);
        Permission::create(['name' => 'edit support ticket']);
        Permission::create(['name' => 'delete support ticket']);

        //Backups. view,delete
        Permission::create(['name' => 'view backup']);
        Permission::create(['name' => 'delete backup']);
        Permission::create(['name' => 'create backup']);

        //View activity log
        Permission::create(['name' => 'view activity log']);


    }
}
