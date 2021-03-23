<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Core\InstalacaoModel;
use Carbon\Carbon;

trait ImportadorInfraEstruturaTrait
{
    private $columnsInfra = array(
        array("id" => "id", "column" => null),
        array("id" => "unidade_produtiva_id", "column" => null),
        array("id" => "instalacao_tipo_id", "column" => null),
        array("id" => "descricao", "column" => null),
        array("id" => "quantidade", "column" => null),
        array("id" => "area", "column" => null),
        array("id" => "observacao", "column" => null),
        array("id" => "localizacao", "column" => null),
    );

    private function importInfraEstrutura($spreadsheet, $numberSheet)
    {
        $createdAt = Carbon::now();

        $sheet = $spreadsheet->getSheet($numberSheet);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $columns = $this->getColumnsIds($sheet, $highestColumn);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsInfra = $this->prepareFillColumns($this->columnsInfra, $columns);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($this->columnsInfra, $columns)) {
            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                if (!$this->getValueColumn($rowData, $this->columnsInfra, 'id')) {
                    continue;
                }

                try {
                    $dataInfra = [
                        'id' => $this->getValueColumn($rowData, $this->columnsInfra, 'id'),
                        'uid' => $this->getValueColumn($rowData, $this->columnsInfra, 'id'),
                        'unidade_produtiva_id' => $this->getValueColumnOrNull($rowData, $this->columnsInfra, 'unidade_produtiva_id'),
                        'instalacao_tipo_id' => $this->getValueColumnOrNull($rowData, $this->columnsInfra, 'instalacao_tipo_id'),
                        'descricao' => $this->getValueColumn($rowData, $this->columnsInfra, 'descricao'),
                        'quantidade' => $this->formatTotal($this->getValueColumn($rowData, $this->columnsInfra, 'quantidade')),
                        'area' => $this->formatTotal($this->getValueColumn($rowData, $this->columnsInfra, 'area')),
                        'observacao' => $this->getValueColumn($rowData, $this->columnsInfra, 'observacao'),
                        'localizacao' => $this->getValueColumn($rowData, $this->columnsInfra, 'localizacao'),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    InstalacaoModel::insert($dataInfra);
                } catch (\Exception $e) {
                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataInfra['id'] . '] DUPLICADO - instalacoes';
                    } else {
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de "Infra Estrutura" não corresponde ao template definido');
        }
    }
}
