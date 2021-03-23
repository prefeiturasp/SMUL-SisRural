<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Core\ColaboradorModel;
use Carbon\Carbon;

trait ImportadorPessoasTrait
{
    private $columnsPessoas = array(
        array("id" => "id", "column" => null),
        array("id" => "unidade_produtiva_id", "column" => null),
        array("id" => "nome", "column" => null),
        array("id" => "relacao_id", "column" => null),
        array("id" => "cpf", "column" => null),
        array("id" => "funcao", "column" => null),
        array("id" => "dedicacao_id", "column" => null),
    );

    private function importPessoas($spreadsheet, $numberSheet)
    {
        $createdAt = Carbon::now();

        $sheet = $spreadsheet->getSheet($numberSheet);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $columns = $this->getColumnsIds($sheet, $highestColumn);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsPessoas = $this->prepareFillColumns($this->columnsPessoas, $columns);

        // dd($columns, $this->columnsPessoas);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($this->columnsPessoas, $columns)) {
            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                if (!$this->getValueColumn($rowData, $this->columnsPessoas, 'id')) {
                    continue;
                }

                try {
                    $dataPessoas = [
                        'id' => $this->getValueColumn($rowData, $this->columnsPessoas, 'id'),
                        'uid' => $this->getValueColumn($rowData, $this->columnsPessoas, 'id'),
                        'unidade_produtiva_id' => $this->getValueColumn($rowData, $this->columnsPessoas, 'unidade_produtiva_id'),
                        'nome' => $this->getValueColumn($rowData, $this->columnsPessoas, 'nome'),
                        'relacao_id' => $this->getValueColumnOrNull($rowData, $this->columnsPessoas, 'relacao_id'),
                        'cpf' => $this->formatOnlyNumbers($this->getValueColumn($rowData, $this->columnsPessoas, 'cpf')),
                        'funcao' => $this->getValueColumn($rowData, $this->columnsPessoas, 'funcao'),
                        'dedicacao_id' => $this->getValueColumnOrNull($rowData, $this->columnsPessoas, 'dedicacao_id'),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    ColaboradorModel::insert($dataPessoas);
                } catch (\Exception $e) {
                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataPessoas['id'] . '] DUPLICADO - colaboradores';
                    } else {
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de "Pessoas" não corresponde ao template definido');
        }
    }
}
