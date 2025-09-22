<?php

namespace App\Extra\DevTools\Services\Traits;

trait Schema
{

    /**
     * Сгенерировать Models Schema
     */
    public function modelsSchemaGenerate(): string
    {

        return $this->artisanCommandRun('revered:schema');

    }

}
