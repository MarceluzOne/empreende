<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database (só ambiente local).
     *
     * Sem `WithoutModelEvents`: os models usam HasUuids, que gera o id no
     * evento `creating` — com os eventos desligados o insert sai sem id.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
