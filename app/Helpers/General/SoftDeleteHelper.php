<?php

namespace App\Helpers\General;

use Carbon\Carbon;

class SoftDeleteHelper
{
    /**
     * Fix Soft Delete Many to Many relationship
     *
     * @param $fnModel (objeto belongsToMany)
     * @param $fkId (id do relacionamento, que liga as duas tabelas)
     * @param $newData (dados p/ salvar)
     */
    public static function syncSoftDelete($fnModel, $fkId, $newData)
    {
        $table = $fnModel->getTable();

        $foreignPivotKey = $fnModel->getForeignPivotKeyName(); //Ex: unidade_produtiva_id
        $parentKey = $fnModel->getParentKeyName();

        $relatedPivotKey = $fnModel->getRelatedPivotKeyName(); //Ex: canal_comercializacao_id
        $relatedKey = $fnModel->getRelatedKeyName(); //Ex: id

        $values = $fnModel->get()->pluck($relatedKey)->toArray();

        $valuesFound = array_intersect($values, @$newData ? $newData : []);

        \DB::table($table)->where($foreignPivotKey, $fkId)->update(['updated_at' => Carbon::now(), 'deleted_at' => Carbon::now()]);
        \DB::table($table)->where($foreignPivotKey, $fkId)->whereIn($relatedPivotKey, $valuesFound)->update(['updated_at' => Carbon::now(), 'deleted_at' => null]);

        $fnModel->syncWithoutDetaching(@$newData);
    }
}
