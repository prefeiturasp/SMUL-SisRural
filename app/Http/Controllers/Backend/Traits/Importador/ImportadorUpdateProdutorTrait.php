<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Core\AssistenciaTecnicaTipoModel;
use App\Models\Core\CidadeModel;
use App\Models\Core\EstadoModel;
use App\Models\Core\ProdutorModel;

trait ImportadorUpdateProdutorTrait
{
    private $columnsProdutor = array(
        array("id" => "id", "column" => null),
        array("id" => "nome", "column" => null),
        array("id" => "cpf", "column" => null),
        array("id" => "telefone_1", "column" => null),
        array("id" => "telefone_2", "column" => null),
        array("id" => "nome_social", "column" => null),
        array("id" => "email", "column" => null),
        array("id" => "genero_id", "column" => null),
        array("id" => "etinia_id", "column" => null),
        array("id" => "fl_portador_deficiencia", "column" => null),
        array("id" => "portador_deficiencia_obs", "column" => null),
        array("id" => "data_nascimento", "column" => null),
        array("id" => "rg", "column" => null),
        array("id" => "fl_cnpj", "column" => null),
        array("id" => "cnpj", "column" => null),
        array("id" => "fl_nota_fiscal_produtor", "column" => null),
        array("id" => "nota_fiscal_produtor", "column" => null),
        array("id" => "fl_agricultor_familiar", "column" => null),
        array("id" => "fl_agricultor_familiar_dap", "column" => null),
        array("id" => "agricultor_familiar_numero", "column" => null),
        array("id" => "agricultor_familiar_data", "column" => null),
        array("id" => "fl_assistencia_tecnica", "column" => null),
        array("id" => "assistencia_tecnica_tipo_id", "column" => null),
        array("id" => "assistencia_tecnica_periodo", "column" => null),
        array("id" => "fl_comunidade_tradicional", "column" => null),
        array("id" => "comunidade_tradicional_obs", "column" => null),
        array("id" => "fl_internet", "column" => null),
        array("id" => "fl_tipo_parceria", "column" => null),
        array("id" => "tipo_parcerias_obs", "column" => null),
        array("id" => "fl_reside_unidade_produtiva", "column" => null),
        array("id" => "cep", "column" => null),
        array("id" => "endereco", "column" => null),
        array("id" => "bairro", "column" => null),
        array("id" => "municipio", "column" => null),
        array("id" => "estado", "column" => null),
        array("id" => "status", "column" => null),
        array("id" => "status_observacao", "column" => null),
        array("id" => "renda_agricultura_id", "column" => null),
        array("id" => "rendimento_comercializacao_id", "column" => null),
        array("id" => "outras_fontes_renda", "column" => null),
        array("id" => "grau_instrucao_id", "column" => null),
        array("id" => "created_at", "column" => null),
    );

    private function importUpdateProdutores($spreadsheet, $numberSheet)
    {
        $sheet = $spreadsheet->getSheet($numberSheet);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $columns = $this->getColumnsIds($sheet, $highestColumn);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsProdutor = $this->prepareFillColumns($this->columnsProdutor, $columns);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($this->columnsProdutor, $columns)) {

            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                if (@!$rowData[0]) {
                    continue;
                }

                $id = $this->getValueColumn($rowData, $this->columnsProdutor, 'id');

                try {
                    $dataProdutor = [
                        'fl_cnpj' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_cnpj')),
                        'fl_nota_fiscal_produtor' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_nota_fiscal_produtor')),
                        'fl_agricultor_familiar_dap' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_agricultor_familiar_dap')),
                        'fl_internet' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_internet')),
                        'fl_tipo_parceria' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_tipo_parceria')),

                        'fl_portador_deficiencia' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_portador_deficiencia')),
                        'fl_agricultor_familiar' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_agricultor_familiar')),
                        'fl_assistencia_tecnica' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_assistencia_tecnica')),
                        'fl_comunidade_tradicional' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_comunidade_tradicional')),
                        'fl_reside_unidade_produtiva' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_reside_unidade_produtiva'))
                    ];

                    ProdutorModel::where('id', $id)->first()->update($dataProdutor);
                } catch (\Exception $e) {
                    // var_dump($dataProdutor['id']);
                    $errors[] = '[' . $id . '] ' . $e->getMessage();
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de produtores não corresponde ao template definido');
        }
    }

    protected function formatSimNaoSemResposta($value)
    {
        $value = strtolower($this->removeAccents($value));
        if ($value == 'sim' || $value == 's' || $value == '1' || $value == '1- sim') {
            return 1;
        } else if ($value == 'nao' || $value == 'n' || $value == '0') {
            return 0;
        } else {
            return null;
        }
    }
}
