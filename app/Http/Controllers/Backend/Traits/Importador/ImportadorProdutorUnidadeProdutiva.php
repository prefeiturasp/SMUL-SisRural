<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Core\ProdutorUnidadeProdutivaModel;
use Carbon\Carbon;

trait ImportadorProdutorUnidadeProdutiva
{
    private $columnsProdutorUnidadeProdutiva = array(
        array("id" => "id", "column" => null),
        array("id" => "produtor_id", "column" => null),
        array("id" => "unidade_produtiva_id", "column" => null),
        array("id" => "tipo_posse_id", "column" => null),
    );

    private function importProdutoresUnidadesProdutivas($spreadsheet, $numberSheet)
    {
        $createdAt = Carbon::now();

        $sheet = $spreadsheet->getSheet($numberSheet);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $columns = $this->getColumnsIds($sheet, $highestColumn);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsProdutorUnidadeProdutiva = $this->prepareFillColumns($this->columnsProdutorUnidadeProdutiva, $columns);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($this->columnsProdutorUnidadeProdutiva, $columns)) {
            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                if (!$this->getValueColumn($rowData, $this->columnsProdutorUnidadeProdutiva, 'id')) {
                    continue;
                }

                try {
                    $dataProdutorUnidProdutiva = [
                        'id' => $this->getValueColumn($rowData, $this->columnsProdutorUnidadeProdutiva, 'id'),
                        'uid' => $this->getValueColumn($rowData, $this->columnsProdutorUnidadeProdutiva, 'id'),
                        'produtor_id' => $this->getValueColumn($rowData, $this->columnsProdutorUnidadeProdutiva, 'produtor_id'),
                        'unidade_produtiva_id' => $this->getValueColumn($rowData, $this->columnsProdutorUnidadeProdutiva, 'unidade_produtiva_id'),
                        'tipo_posse_id' => $this->getValueColumn($rowData, $this->columnsProdutorUnidadeProdutiva, 'tipo_posse_id'),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    ProdutorUnidadeProdutivaModel::insert($dataProdutorUnidProdutiva);
                } catch (\Exception $e) {
                    // var_dump($dataProdutorUnidProdutiva['id']);
                    // var_dump($e->getMessage());

                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataProdutorUnidProdutiva['id'] . '] DUPLICADO - produtor_unidade_produtivas';
                    } else {
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de "Produtor vs Unidade Produtiva" não corresponde ao template definido');
        }
    }
}
