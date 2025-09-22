<?php

namespace App\Extra\Services\Traits\BREAD;

use Illuminate\Support\Facades\DB;

trait Delete
{

    /**
     * Удаление
     * @throws \Throwable
     */
    public function delete( $model ): ?bool
    {

        if( is_numeric($model) )
            $model = $this->getOneById($model);


        return DB::transaction(function () use ($model){

            $this->beforeDelete($model);

            events()->setClientEvent($this->getEventName('delete'), $model);

            $result = $model->delete();

            $this->afterDelete($model);

            return $result;

        });

    }


    /**
     * Запускается перед удалением (мы можем права доступа проверить здесь, например)
     */
    protected function beforeDelete( $model ): void
    {

    }



    /**
     * Запускается после удаления
     */
    protected function afterDelete( $model ): void
    {


    }



    /**
     * Восстановить из архива (для softDeletes моделей)
     * @throws \Throwable
     */
    public function restore( $model ){

        if( is_numeric($model) ) {

            $model_obj = $this->getOneFromTrash($model, false);

            if( !$model_obj ){

                $model_obj = $this->getOneById($model, false);

                if( $model_obj ){

                    return $model_obj;

                }else{

                    throw new \Exception('Запись была полностью удалена, ее не возможно восстановить.');

                }

            }

            $model = $model_obj;

        }

        return DB::transaction(function () use ($model){

            $this->beforeRestore($model);

            events()->setClientEvent($this->getEventName('restore'), $model);

            $model->restore();

            $this->afterRestore($model);

            return $model;

        });

    }


    /**
     * Запускается перед восстановлением из архива (для softDeletes моделей)
     */
    protected function beforeRestore( $model ): void
    {

    }


    /**
     * Запускается после восстановлением из архива (для softDeletes моделей)
     */
    protected function afterRestore( $model ): void
    {

    }


    /**
     * Полностью удалить (для softDeletes моделей)
     * @throws \Throwable
     */
    public function forceDelete( $model ): ?bool
    {

        if( is_numeric($model) ) {

            $model = $this->getOneFromTrash($model, false);

            if( !$model )
                throw new \Exception('Не найдено в архиве для полного удаления.');

        }

        return DB::transaction(function () use ($model){

            $this->beforeForceDelete($model);

            events()->setClientEvent($this->getEventName('delete'), $model);
            events()->setClientEvent($this->getEventName('force_delete'), $model);

            $result = $model->forceDelete();

            $this->afterForceDelete($model);

            return $result;

        });

    }


    /**
     * Запускается перед полным удалением (для softDeletes моделей)
     */
    protected function beforeForceDelete( $model ): void
    {

    }


    /**
     * Запускается после полного удаления (для softDeletes моделей)
     */
    protected function afterForceDelete( $model ): void
    {

    }

}
