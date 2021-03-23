<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Core\UnidadeProdutivaModel;

trait ImportadorUnidadesProdutivas
{
    private $columnsUnidadeProdutiva = array(
        array("id" => "id", "column" => null),
        array("id" => "nome", "column" => null),
        array("id" => "cep", "column" => null),
        array("id" => "endereco", "column" => null),
        array("id" => "bairro", "column" => null),
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

    private function importUnidadesProdutivas($spreadsheet, $numberSheet)
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

                try {
                    $dataUnidadeProdutiva = [
                        'id' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'id'),
                        'uid' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'id'),
                        'nome' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'nome'),
                        'cep' => $this->formatCep($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'cep')),
                        'endereco' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'endereco'),
                        'bairro' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'bairro'),
                        'cidade_id' => $this->formatMunicipio($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'municipio')),
                        'estado_id' => $this->formatEstado($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'estado')),
                        'lat' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'lat'),
                        'lng' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'lng'),
                        'fl_certificacoes' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_certificacoes')),
                        'certificacoes_descricao' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'certificacoes_descricao'),
                        'fl_car' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_car'),
                        'car' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'car'),
                        'fl_ccir' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_ccir')),
                        'fl_itr' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_itr')),
                        'fl_matricula' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_matricula')),
                        'upa' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'upa'),
                        'fl_pressao_social' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_pressao_social')),
                        'pressao_social_descricao' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'pressao_social_descricao'),
                        'fl_comercializacao' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_comercializacao')),
                        'gargalos' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'gargalos'),
                        'outorga_id' => $this->formatOutorga($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'outorga')),
                        'fl_risco_contaminacao' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_risco_contaminacao')),
                        'risco_contaminacao_observacoes' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'risco_contaminacao_observacoes'),
                        'area_total_solo' => $this->formatTotal($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'area_total_solo')),
                        'fl_producao_processa' => $this->formatProducaoProcessa($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'fl_producao_processa')),
                        'producao_processa_descricao' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'producao_processa_descricao'),
                        'outros_usos_descricao' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'outros_usos_descricao'),
                        'bacia_hidrografica' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'bacia_hidrografica'),
                        'status' => $this->getStatusAtivoInativo($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'status')),
                        'status_observacao' => $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'status_observacao'),

                        'created_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'created_at')),
                        'updated_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'created_at')),
                    ];

                    $model = UnidadeProdutivaModel::insert($dataUnidadeProdutiva);

                    $model = UnidadeProdutivaModel::where('id', $this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'id'))->first();

                    //Não precisa do sync porque é feito via Unidade Operacional no final da importação

                    $model->certificacoes()->sync($this->formatListValues($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'certificacoes')));
                    $model->pressaoSociais()->sync($this->formatListValues($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'pressaoSociais')));
                    $model->canaisComercializacao()->sync($this->formatListValues($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'canaisComercializacao')));
                    $model->tiposFonteAgua()->sync($this->formatListValues($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'tiposFonteAgua')));
                    $model->riscosContaminacaoAgua()->sync($this->formatListValues($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'riscosContaminacaoAgua')));
                    $model->solosCategoria()->sync($this->formatListValues($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'solosCategoria')));
                    $model->esgotamentoSanitarios()->sync($this->formatListValues($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'esgotamentoSanitarios')));
                    $model->residuoSolidos()->sync($this->formatListValues($this->getValueColumn($rowData, $this->columnsUnidadeProdutiva, 'residuoSolidos')));
                } catch (\Exception $e) {
                    // var_dump($dataUnidadeProdutiva['id']);
                    // var_dump($e->getMessage());

                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataUnidadeProdutiva['id'] . '] DUPLICADO - unidade_produtivas';
                    } else {
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de "Unidades Produtivas" não corresponde ao template definido');
        }
    }

    protected function formatProducaoProcessa($value)
    {
        $value = strtolower($this->removeAccents($value));
        if ($value == 'sim') {
            return 'sim';
        } else if ($value == 'nao') {
            return 'nao';
        } else if ($value == 'nao tem interesse') {
            return 'nao_tem_interesse';
        } else {
            return null;
        }
    }

    protected function formatListValues($values)
    {
        $list = explode(",", str_replace(" ", ",", str_replace(".", ",", $values)));

        foreach ($list as $k => $v) {
            $list[$k] = trim($v);
        }

        return array_filter($list);
    }

    protected function formatTotal($value)
    {
        $value = trim($value . '');

        if (is_numeric($value)) {
            return $value;
        } else {
            return null;
        }
    }

    protected function formatOutorga($value)
    {
        $value = trim(strtolower($this->removeAccents($value)));

        if ($value == 'sim') {
            return 1;
        } else if ($value == 'nao') {
            return 2;
        } else {
            return 3;
        }
    }
}
