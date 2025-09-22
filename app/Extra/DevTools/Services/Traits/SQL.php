<?php

namespace App\Extra\DevTools\Services\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

trait SQL
{


    /**
     * Скачать и импортировать SQL file
     * @throws \Exception
     */
    public function downloadAndImportSqlFile( string $download_link, string $filename ): void
    {

        $this->phpIniSetToImport();

        $file = Http::withoutVerifying()->timeout(600)->get($download_link);

        if( !$file->successful() )
            throw new \Exception('Something went wrong downloading the file: '.$download_link);


        Storage::put($filepath = 'temp/'.$filename, $file->body());
        $filepath = base_path('storage/app/'.$filepath);

        $paths_to_delete = [$filepath];

        if( str($filepath)->endsWith('.zip') ){

            $new_filepath = str($filepath)->replace('.sql.zip', '.sql')->toString();

            if( !file_exists($new_filepath) ){

                $zip_archive = new \ZipArchive();
                $zip_archive->open($filepath);
                $zip_archive->extractTo(str($filepath)->explode('/')->slice(0, -1)->implode('/'));
                $zip_archive->close();

            }

            $paths_to_delete[] = $new_filepath;
            $filepath = $new_filepath;

        }

        $this->importSqlFile($filepath);


        foreach( $paths_to_delete as $path )
            unlink($path);

    }


    /**
     * Import Database dump
     * Передаеться путь к .sql файлу (.zip, .gz не подходят)
     * @throws \Exception
     */
    public function importSqlFile( string $filepath ): void
    {
        $this->phpIniSetToImport();

        if( !file_exists($filepath) )
            throw new \Exception('File not found: '.$filepath);

        $filename = last(explode('/', $filepath));

        if( !str($filepath)->endsWith('.sql') )
            throw new \Exception('File should have .sql extension.');

        $mysql_cmd = "--user=".config('env.db.username')." --password=".config('env.db.password')." --host=".config('env.db.host')." --database ".config('env.db.database')." --port ".config('env.db.port')." < $filepath";

        $result = Process::timeout(600)->run("mysql $mysql_cmd 2>&1")->output();

        if( str($result)->lower()->contains('command not found') || str($result)->lower()->contains('is not recognized') ){

            $result_mamp = Process::run("/Applications/MAMP/Library/bin/mysql $mysql_cmd 2>&1")->output();
            if( str($result_mamp)->lower()->contains('command not found') || str($result_mamp)->lower()->contains('is not recognized') ){

                throw new \Exception('You should install mysql command in console. Error: "' . $result . '"');

            }else{

                $result = $result_mamp;

            }
        }

        if( str($result)->lower()->contains('error') )
            throw new \Exception($filename.' - '.$result);

        if( !$result )
            throw new \Exception($filename.' - Something went wrong when importing sql file .');

    }


}
