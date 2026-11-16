<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('modules')->insert([
            [
                'name'          => 'User Management',
                'short_name'    => 'UM',
                'group_name'    => 'System & Access Control',
                'group_order'   => 1,
                'description'   => 'Create and manage admin, sub-admins, and users.',
                'icon'          => 'bi bi-people-fill',
                'dashboard_url' => 'um/dashboard',
                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Project Setup',
                'short_name'    => 'PS',
                'group_name'    => 'Construction & Operations',
                'group_order'   => 2,
                'description'   => 'Manage construction projects, clients, owners, and site timelines.',
                'icon'          => 'bi bi-buildings-fill',
                'dashboard_url' => 'ps/dashboard',
                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'name'          => 'Petty Cash',
                'short_name'    => 'PC',
                'group_name'    => 'Construction & Operations',
                'group_order'   => 3,
                'description'   => 'Manage daily site expenses, labour advances, and miscellaneous cash transactions.',
                'icon'          => 'bi bi-wallet2',
                'dashboard_url' => 'pc/dashboard',
                'status'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}