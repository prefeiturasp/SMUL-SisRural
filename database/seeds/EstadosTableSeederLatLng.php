<?php

use Illuminate\Database\Seeder;

class EstadosTableSeederLatLng extends Seeder
{
    use TruncateTable,
        DisableForeignKeys;


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        $this->truncate('estados');
        $this->truncate('cidades');

        if (DB::table('cidades')->count() === 0) {

            $path = 'resources/sql/cidades.sql';
            $file = new SplFileObject($path);

            while (!$file->eof()) {
                try {
                    DB::unprepared($file->fgets());
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }

            $file = null;
        }

        if (DB::table('estados')->count() === 0) {
            $path = 'resources/sql/estados.sql';
            $file = new SplFileObject($path);

            while (!$file->eof()) {
                try {
                    DB::unprepared($file->fgets());
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }

            $file = null;
        }
    }
}
