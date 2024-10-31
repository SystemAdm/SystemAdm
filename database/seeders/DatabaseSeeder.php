<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /*User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/
        $admin = Role::create(['name' => 'Administrator']);
        $styretLeder = Role::create(['name' => 'Styret Leder']);
        $styret = Role::create(['name' => 'Styret']);
        $crew = Role::create(['name' => 'Crew']);
        $member = Role::create(['name' => 'Medlem']);
        $guest = Role::create(['name' => 'Gjest']);
        $outcast = Role::create(['name' => 'Utestengt']);

        Permission::create(['name' => 'events.create'])->syncRoles($admin, $styretLeder, $styret, $crew);
        Permission::create(['name' => 'events.edit'])->syncRoles($admin, $styretLeder, $styret, $crew);
        Permission::create(['name' => 'events.show'])->syncRoles($admin, $styretLeder, $styret, $crew,$member,$guest);
        Permission::create(['name' => 'events.index'])->syncRoles($admin, $styretLeder, $styret, $crew,$member,$guest);
        Permission::create(['name' => 'events.admin'])->syncRoles($admin, $styretLeder, $styret, $crew);

        Permission::create(['name' => 'users.create'])->syncRoles($admin, $styretLeder, $styret, $crew,$member,$guest);
        Permission::create(['name' => 'users.edit'])->syncRoles($admin, $styretLeder, $styret, $crew);
        Permission::create(['name' => 'users.show'])->syncRoles($admin, $styretLeder, $styret, $crew,$member,$guest);
        Permission::create(['name' => 'users.index'])->syncRoles($admin, $styretLeder, $styret, $crew);
        Permission::create(['name' => 'users.admin'])->syncRoles($admin, $styretLeder, $styret);
    }
}
