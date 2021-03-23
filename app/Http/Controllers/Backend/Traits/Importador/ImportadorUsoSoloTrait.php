<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Core\UnidadeProdutivaCaracterizacaoModel;
use Carbon\Carbon;

trait ImportadorUsoSoloTrait
{
    private $columnsUsoSolo = array(
        array("id" => "id", "column" => null),
        array("id" => "unidade_produtiva_id", "column" => null),
        array("id" => "solo_categoria_id", "column" => null),
        array("id" => "area", "column" => null),
        array("id" => "quantidade", "column" => null),
        array("id" => "especies", "column" => null),
        array("id" => "descricao", "column" => null),
    );

    private function importUsoSolo($spreadsheet, $numberSheet)
    {
        $createdAt = Carbon::now();

        $sheet = $spreadsheet->getSheet($numberSheet);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $columns = $this->getColumnsIds($sheet, $highestColumn);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsUsoSolo = $this->prepareFillColumns($this->columnsUsoSolo, $columns);
        //dd($columns, $this->columnsUsoSolo);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($this->columnsUsoSolo, $columns)) {
            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                if (!$this->getValueColumn($rowData, $this->columnsUsoSolo, 'id')) {
                    continue;
                }

                try {
                    $dataUsoSolo = [
                        'id' => $this->getValueColumn($rowData, $this->columnsUsoSolo, 'id'),
                        'uid' => $this->getValueColumn($rowData, $this->columnsUsoSolo, 'id'),
                        'unidade_produtiva_id' => $this->getValueColumn($rowData, $this->columnsUsoSolo, 'unidade_produtiva_id'),
                        'solo_categoria_id' => $this->getValueColumn($rowData, $this->columnsUsoSolo, 'solo_categoria_id'),
                        'area' => $this->getValueColumn($rowData, $this->columnsUsoSolo, 'area'),
                        'quantidade' => $this->getValueColumn($rowData, $this->columnsUsoSolo, 'quantidade'),
                        'descricao' => $this->getValueColumn($rowData, $this->columnsUsoSolo, 'descricao'),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    UnidadeProdutivaCaracterizacaoModel::insert($dataUsoSolo);
                } catch (\Exception $e) {
                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataUsoSolo['id'] . '] DUPLICADO - unidade_produtiva_caracterizacoes';
                    } else {
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de "Uso Solo" não corresponde ao template definido');
        }
    }
}
