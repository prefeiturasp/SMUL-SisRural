<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Auth\User;

trait ImportadorUsuariosTrait
{
    private $columnsUsuarios = array(
        array("id" => "id", "column" => null),
        array("id" => "first_name", "column" => null),
        array("id" => "last_name", "column" => null),
        array("id" => "email", "column" => null),
        array("id" => "cpf", "column" => null),
        array("id" => "password", "column" => null),
        array("id" => "phone", "column" => null),
        array("id" => "address", "column" => null),
        array("id" => "work", "column" => null),
        array("id" => "dominio", "column" => null),
        array("id" => "unidade_operacional", "column" => null),
        array("id" => "papel", "column" => null),
    );

    private function importUsuario($spreadsheet, $numberSheet)
    {
        $defaultCollumnsToCheck = $this->columnsUsuarios;

        $sheet = $spreadsheet->getSheet($numberSheet);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $columns = $this->getColumnsIds($sheet, $highestColumn, 1);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsUsuarios = $this->prepareFillColumns($this->columnsUsuarios, $columns);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($defaultCollumnsToCheck, $columns)) {

            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 3; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                $usuarioId = $this->getValueColumn($rowData, $this->columnsUsuarios, 'id');

                if (!$this->getValueColumn($rowData, $this->columnsUsuarios, 'email')) {
                    continue;
                }

                try {
                    $papel = $this->getValueColumn($rowData, $this->columnsUsuarios, 'papel');
                    $dominioId = $this->getValueColumn($rowData, $this->columnsUsuarios, 'dominio');
                    $unidadeOperacionalIds = explode(",", str_replace(".", ",", $this->getValueColumn($rowData, $this->columnsUsuarios, 'unidade_operacional')));

                    $model = User::create([
                        'id' => $usuarioId,
                        'first_name' => $this->getValueColumn($rowData, $this->columnsUsuarios, 'first_name'),
                        'last_name' => $this->getValueColumn($rowData, $this->columnsUsuarios, 'last_name'),
                        'email' => $this->getValueColumn($rowData, $this->columnsUsuarios, 'email'),
                        'document' => $this->getValueColumn($rowData, $this->columnsUsuarios, 'cpf'),
                        'password' => $this->getValueColumn($rowData, $this->columnsUsuarios, 'password'),
                        'phone' => $this->getValueColumn($rowData, $this->columnsUsuarios, 'phone'),
                        'address' => $this->getValueColumn($rowData, $this->columnsUsuarios, 'address'),
                        'work' => $this->getValueColumn($rowData, $this->columnsUsuarios, 'work'),
                        'confirmation_code' => md5(uniqid(mt_rand(), true)),
                        'confirmed' => true,
                    ]);


                    if ($papel == 'tecnico') {
                        $model->assignRole(config('access.users.technician_role'));
                    } else if ($papel  == 'unidade_operacional') {
                        $model->assignRole(config('access.users.operational_unit_role'));
                    } else if ($papel == 'dominio') {
                        $model->assignRole(config('access.users.domain_role'));
                    }

                    if ($papel == 'tecnico' || $papel == 'unidade_operacional') {
                        foreach ($unidadeOperacionalIds as $idUnidadeOperacional) {
                            \App\Models\Core\UserUnidadeOperacionalModel::insert([
                                ['user_id' => $usuarioId, 'unidade_operacional_id' => $idUnidadeOperacional],
                            ]);
                        }
                    }

                    if ($papel == 'dominio') {
                        \App\Models\Core\UserDominioModel::insert([
                            ['user_id' => $usuarioId, 'dominio_id' => $dominioId],
                        ]);
                    }
                } catch (\Exception $e) {
                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $usuarioId . '] DUPLICADO - usuarios';
                    } else {
                        echo $e->getMessage() . '<br>';
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela do Usuários não corresponde ao template definido');
        }
    }
}
