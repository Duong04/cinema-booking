<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ["super admin", "staff" , "customer"]; 
        for ($i=0; $i < count($roles) ; $i++) { 
            Role::create([
                'name' => $roles[$i],
            ]);
        };
    }
}
