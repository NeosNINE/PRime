<?php

namespace App\Extra\DevTools\Services\Traits;

trait SecretConfig
{

    /**
     * Получить Secret Config
     */
    public function getSecretConfigContent(): string
    {

        if( !roles()->isSuperAdmin() )
            abort(403);

        checkConfirmPassword();

        $this->createSecretConfigFile();

        $config = config(settings()::CONFIG_SECRET_FILE_NAME);
        $config = settings()->decryptValue($config);

        if( !count($config) )
            return "There's nothing here yet.";

        return json_encode(
            $config,
            JSON_PRETTY_PRINT);

    }


    /**
     * Создать Secret Config
     */
    public function createSecretConfigFile(): void
    {

        $secret_config_path = base_path( 'config/'.settings(false)::CONFIG_SECRET_FILE_NAME.'.php' );

        if( !file_exists($secret_config_path) ){

            $this->saveFile($secret_config_path, '<?php

return [

];');

        }

    }

}
