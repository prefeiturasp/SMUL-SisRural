<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Core\UnidadeProdutivaModel;

trait ImportadorUpdateUnidadesProdutivasTrait
{
    private $columnsUnidadeProdutiva = array(
        array("id" => "id", "column" => null),
        array("id" => "nome", "column" => null),
        array("id" => "cep", "column" => null),
        array("id" => "endereco", "column" => null),
        array("id" => "bairro", "column" => null),
        array("id" => "subprefeitura", "column" => null),
        array("id" => "municipio", "column" => null),
        array("id" => "estado", "column" => null),
        array("id" => "lat", "column" => null),
        array("id" => "lng", "column" => null),
        array("id" => "fl_certificacoes", "column" => null),
        array("id" => "certificacoes", "column" => null),
        array("id" => "certificacoes_descricao", "column" => null),
        array("id" => "fl_car", "column" => null),
        array("id" => "car", "column" => null),
        array("id" => "fl_ccir", "column" => null),
        array("id" => "fl_itr", "column" => null),
        array("id" => "fl_matricula", "column" => null),
        array("id" => "upa", "column" => null),
        array("id" => "fl_pressao_social", "column" => null),
        array("id" => "pressaoSociais", "column" => null),
        array("id" => "pressao_social_descricao", "column" => null),
        array("id" => "fl_comercializacao", "column" => null),
        array("id" => "canaisComercializacao", "column" => null),
        array("id" => "gargalos", "column" => null),
        array("id" => "outorga", "column" => null),
        array("id" => "tiposFonteAgua", "column" => null),
        array("id" => "fl_risco_contaminacao", "column" => null),
        array("id" => "riscosContaminacaoAgua", "column" => null),
        array("id" => "risco_contaminacao_observacoes", "column" => null),
        array("id" => "area_total_solo", "column" => null),
        array("id" => "fl_producao_processa", "column" => null),
        array("id" => "producao_processa_descricao", "column" => null),
        array("id" => "solosCategoria", "column" => null),
        array("id" => "outros_usos_descricao", "column" => null),
        array("id" => "bacia_hidrografica", "column" => null),
        array("id" => "status", "column" => null),
        array("id" => "status_observacao", "column" => null),
        array("id" => "esgotamentoSanitarios", "column" => null),
        array("id" => "residuoSolidos", "column" => null),
        array("id" => "especieProduzidas", "column" => null),
        array("id" => "diversidadeUsoTerra", "column" => null),
        array("id" => "agrobiodiversidade", "column" => null),
        array("id" => "created_at", "column" => null),
    );

    private function importUpdateUnidadesProdutivas($spreadsheet, $numberSheet)
    {
        $sheet = $spreadsheet->getSheet($numberSheet);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $columns = $this->getColumnsIds($sheet, $highestColumn);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsUnidadeProdutiva = $this->prepareFillColumns($this->columnsUnidadeProdutiva, $columns);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($this->columnsUnidadeProdutiva, $columns)) {

            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                if (@!$rowData[0]) {
                    continue;
                }

                $id = $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'id');

                try {
                    $dataUnidadeProdutiva = [
                        'fl_car' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_car'),
                        'fl_ccir' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_ccir')),
                        'fl_itr' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_itr')),
                        'fl_matricula' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_matricula')),
                        'fl_pressao_social' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_pressao_social')),
                        'fl_comercializacao' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_comercializacao')),
                        'fl_producao_processa' => $this->formatProducaoProcessa($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_producao_processa')),
                        'fl_certificacoes' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_certificacoes')),
                        'fl_risco_contaminacao' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_risco_contaminacao'))
                    ];

                    UnidadeProdutivaModel::where('id', $id)->first()->update($dataUnidadeProdutiva);
                } catch (\Exception $e) {
                    // var_dump($dataUnidadeProdutiva['id']);
                    $errors[] = '[' . $id . '] ' . $e->getMessage();
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de "Unidades Produtivas" não corresponde ao template definido');
        }
    }
}
