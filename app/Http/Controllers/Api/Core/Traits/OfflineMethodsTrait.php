<?php

namespace App\Http\Controllers\Api\Core\Traits;

use Carbon\Carbon;
use DB;

/**
 * Class UserScope.
 */
trait OfflineMethodsTrait
{

    public function getSqlInsert($table, $data)
    {
        $columns = $this->getColumns($table);
        $joinColumns = join(',', $columns);

        $chunks = array_chunk($data, 5);

        $ret = [];
        foreach ($chunks as $k => $v) {
            $ret[] = "INSERT INTO $table ($joinColumns) VALUES " . $this->getSqlValuesInto($v);
        }

        return $ret;
    }

    private function mysqlTypeToSqlite($type)
    {
        // seleciona a primeira parte de strings tipo varchar(200) => varchar
        $type = explode("(", $type)[0];

        switch ($type) {
            case "varchar":
                return "text";
            case "text":
                return "text";
            case "bigint":
                return 'integer';
            case "tinyint":
                return 'integer';
            case "timestamp":
                return "text";
            case "enum":
                return "text";
            case "char":
                return "text";
            case "int":
                return "integer";
            case "multipolygon":
                return "text";
        }

        return 'text';
    }

    public function getCreateTableV2($table)
    {
        $columns = DB::select('DESCRIBE ' . $table);
        $columns = array_map(function ($column) {
            if ($column->Field === 'uid') {
                return null;
            }
            return ["field" => $column->Field, 'defaultValue' => $column->Default, 'nullable' => $column->Null == 'NO' ? 'NOT NULL' : 'NULL', 'type' => $this->mysqlTypeToSqlite($column->Type)];
        }, $columns);
        $columns = array_values(array_filter($columns, function ($record) {
            return $record !== null;
        }));
        return (array) $columns;
    }

    //https://stackoverflow.com/questions/3890518/convert-mysql-to-sqlite
    /*
        replace ` -> ""
        replace unsigned -> ""
        replace COLLATE utf8mb4_unicode_ci -> ""
        replace AUTO_INCREMENT -> ""

        enum nao importa

        FK nao importou
    */
    public function getCreateTable($table)
    {
        $sql = DB::select('SHOW CREATE TABLE ' . $table);
        $sql = $sql[0]->{'Create Table'};

        $sql = preg_replace('/`/', '', $sql);
        $sql = preg_replace('/PRIMARY KEY/', '@@@', $sql);

        $sql = preg_replace('/unsigned/', '', $sql);
        $sql = preg_replace('/bigint\([0-9]*\)/', 'integer', $sql);
        $sql = preg_replace('/tinyint\([0-9]*\)/', 'integer', $sql);
        $sql = preg_replace('/enum\(.*\)/', 'text', $sql);
        $sql = preg_replace('/timestamp/', 'text', $sql);
        $sql = preg_replace('/multipolygon/', 'text', $sql);
        $sql = preg_replace('/AUTO_INCREMENT/', '', $sql);
        $sql = preg_replace('/ COLLATE utf8mb4_unicode_ci/', '', $sql);
        $sql = preg_replace('/UNIQUE KEY .*,/', '', $sql);
        $sql = preg_replace('/UNIQUE KEY .*\)/', '', $sql);
        $sql = preg_replace('/KEY .*,/', '', $sql);

        $sql = preg_replace('/ON DELETE CASCADE/', '', $sql);

        //FKS
        $sql = preg_replace('/CONSTRAINT .* FOREIGN /', '', $sql);
        $sql = preg_replace('/KEY .* REFERENCES .*\)/', '', $sql);
        $sql = preg_replace('/ENGINE.*/', '', $sql);

        $sql = preg_replace('/@@@/', 'PRIMARY KEY', $sql);
        $sql = preg_replace('/PRIMARY KEY.*(,|)/', 'PRIMARY KEY (id)', $sql);

        $sql = preg_replace('/@@@/', 'PRIMARY KEY', $sql);

        $sql = preg_replace('/id integer  NOT NULL/', 'id text  NOT NULL', $sql);
        $sql = preg_replace('/_id integer/', '_id text', $sql);

        $sql = preg_replace('/uid text  NOT NULL \,/', '', $sql);

        $sql = preg_replace("/\r|\n/", "", $sql);

        return $sql;
    }

    /**
     * PRIVATE
     **/

    private function getSqlValues($data)
    {
        $list = [];
        foreach ($data as $k => $v) {
            //get_object_vars($v);

            $list[] = implode(',', array_map(array($this, 'escape'), $v));
        }

        return $list;
    }

    private function getSqlValuesInto($data)
    {
        $values = $this->getSqlValues($data);

        return '(' . implode('),(', $values) . ')';
    }

    private function getColumns($table)
    {
        $getColumnName = function ($v) {
            return $v->COLUMN_NAME;
        };

        $columns = DB::select('SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME=? AND TABLE_SCHEMA=?', [$table, env('DB_DATABASE')]);

        return array_map($getColumnName, $columns);
    }

    private function escape($v)
    {
        if (!is_numeric($v)) {
            if ($v) {
                return DB::connection()->getPdo()->quote($v);
            } else {
                return "NULL";
            }
        } else if ($v) {
            return $v;
        }
    }

    private function saveBase64Image($folder, $image, $id)
    {
        $img = preg_replace('/^data:image\/\w+;base64,/', '', $image);
        $type = explode(';', $image)[0];
        $type = explode('/', $type)[1]; // png or jpg etc

        $filename = $id . '.' . $type;
        $filepath = $folder . '/' . $filename;

        $success = \Storage::put($filepath, base64_decode($img));
        if (!$success) {
            return null;
        }

        return $filepath;
    }

    /**
     *
     * Cria ou atualiza a entidade
     *
     * @param $class
     * @param $data
     * @param $repository Não é obrigatório, caso não seja passado, será tratado pelo Model
     * @return array
     */

    private function simpleUpdateOrCreate($class, $data, $repository = null, $messageDuplicated = 'Registro duplicado.', $forceTouch = false)
    {
        $MYSQL_DUPLICATE_CODES = array(1062, 23000);

        $success = [];
        $error = [];
        $errorVersion = [];
        $ids = [];

        foreach ($data as $k => $v) {
            try {
                //Força o retorno do model, independente do "scope". Ex: Unid. Produtiva é do usuário (owner_id) mas esta fora do escopo por que registrou errado
                //Resolve problemas de ids duplicados quando o usuário perde o escopo mas ainda tem acesso no mobile
                $model = $class::withTrashed()->withoutGlobalScopes()->find(@$v['id']);

                if ($this->checkUpdatedAt($model, $v)) {
                    $errorVersion[] = ['app' => $v, 'db' => $model];
                    continue;
                }

                $objectNormalized = $this->normalizeObject($v);

                $appId = $id = @$objectNormalized['id'];

                // if (!\is_numeric($appId)) {
                //     unset($objectNormalized['id']);
                // } else {
                //     $id = $objectNormalized['id']; //Caso seja update
                // }

                //Create/Update via Repository
                if ($repository) {
                    if (@$model) {
                        $repository->update($model, $objectNormalized);
                        if ($forceTouch) {
                            $model->touch();
                        }
                    } else {
                        $returnModel = $repository->create($objectNormalized);
                        $id = $returnModel->id;
                    }
                } else { //Create/Update via Model
                    $returnModel = $class::withTrashed()->updateOrCreate(['id' => @$objectNormalized['id']], $objectNormalized);
                    if ($forceTouch) {
                        $returnModel->touch();
                    }

                    $id = $returnModel->id;
                }

                $ids[] = ['appId' => $appId, 'dbId' => $id];
                $success[] = $id;
            } catch (\Exception $e) {
                $message = $e->getMessage();
                //$message = $e->__toString();

                // busca de novo o model para pegar a versão do banco
                $model = $class::withTrashed()->find(@$v['id']);

                if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                    if (@is_callable($messageDuplicated)) {
                        $message = $messageDuplicated($v);
                    } else if (@$messageDuplicated) {
                        $message = $messageDuplicated;
                    }
                }

                $error[] = ['app' => $v, 'db' => $model, 'message' => $message];
            }
        }

        return [
            'error' => $error,
            'errorVersion' => $errorVersion,
            'success' => $success,
            'ids' => $ids
        ];
    }


    /**
     * Retorna se é possível atualizar ou não a entidade. (Através do campo 'updated_at')
     *
     * @param $model
     * @param $object
     * @return bool
     */
    private function checkUpdatedAt($model, $object)
    {
        if ($model && $model->updated_at != Carbon::parse(@$object['updated_at'])) {
            return true;
        }

        return false;
    }

    /**
     * Retorna o objeto normalizado, sem os campos controlados pelo Backend
     *
     * @param $object
     * @return Object
     */
    private function normalizeObject($object)
    {
        unset($object['updated_at']);
        unset($object['created_at']);
        unset($object['uid']);

        //unset($object['deleted_at']); //O deleted_at pode ser alterado no APP

        return $object;
    }


    /**
     * Merge array a partir de uma coluna.
     *
     * @param $data
     * @param $column
     * @return array
     */
    private function mergeOfflineData(&$data, $column)
    {
        $ret = [];
        foreach ($data as $k => &$v) {

            foreach ($v[$column] as $kk => &$vv) {
                if (@$vv['pivot']) {
                    unset($vv['pivot']);
                }
            }

            $ret = array_merge($ret, $v[$column]);
            unset($v[$column]);
        }
        return collect($ret)->unique('id', true)->values()->all();
    }

    private function resolveLinks($column, $data)
    {
        foreach ($data as $k => &$v) {
            $v[$column] =  \Storage::url('/') . $v[$column];
        }
        return $data;
    }
}
