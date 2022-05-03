<?php

    namespace Modules\Size\Database\Seeders;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Seeder;

    class SizeDatabaseSeeder extends Seeder
    {
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
            Model::unguard();
            // $this->call("OthersTableSeeder");
        }
    }
