<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Models\Core\AssistenciaTecnicaTipoModel;
use App\Models\Core\CidadeModel;
use App\Models\Core\EstadoModel;
use App\Models\Core\ProdutorModel;

trait ImportadorProdutorTrait
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

    private function importProdutores($spreadsheet, $numberSheet)
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

                try {
                    $dataProdutor = [
                        'id' => $this->getValueColumn($rowData, $this->columnsProdutor, 'id'),
                        'uid' => $this->getValueColumn($rowData, $this->columnsProdutor, 'id'),
                        'nome' => $this->getValueColumn($rowData, $this->columnsProdutor, 'nome'),
                        'cpf' => $this->formatOnlyNumbers($this->getValueColumn($rowData, $this->columnsProdutor, 'cpf')),
                        'telefone_1' => $this->formatTelefone($this->getValueColumn($rowData, $this->columnsProdutor, 'telefone_1')),
                        'telefone_2' => $this->formatTelefone($this->getValueColumn($rowData, $this->columnsProdutor, 'telefone_2')),
                        'nome_social' => $this->getValueColumn($rowData, $this->columnsProdutor, 'nome_social'),
                        'email' => $this->getValueColumn($rowData, $this->columnsProdutor, 'email'),
                        'genero_id' => $this->formatGenero($this->getValueColumn($rowData, $this->columnsProdutor, 'genero_id')),
                        'etinia_id' => $this->formatEtinia($this->getValueColumn($rowData, $this->columnsProdutor, 'etinia_id')),
                        'fl_portador_deficiencia' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_portador_deficiencia')),
                        'portador_deficiencia_obs' => $this->getValueColumn($rowData, $this->columnsProdutor, 'portador_deficiencia_obs'),
                        'data_nascimento' => $this->formatDateString($this->getValueColumn($rowData, $this->columnsProdutor, 'data_nascimento')),
                        'rg' => $this->getValueColumn($rowData, $this->columnsProdutor, 'rg'),
                        'fl_cnpj' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_cnpj')),
                        'cnpj' => $this->formatOnlyNumbers($this->getValueColumn($rowData, $this->columnsProdutor, 'cnpj')),
                        'fl_nota_fiscal_produtor' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_nota_fiscal_produtor')),
                        'nota_fiscal_produtor' => $this->getValueColumn($rowData, $this->columnsProdutor, 'nota_fiscal_produtor'),
                        'fl_agricultor_familiar' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_agricultor_familiar')),
                        'fl_agricultor_familiar_dap' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_agricultor_familiar_dap')),
                        'agricultor_familiar_numero' => $this->getValueColumn($rowData, $this->columnsProdutor, 'agricultor_familiar_numero'),
                        'agricultor_familiar_data' => $this->getValueColumn($rowData, $this->columnsProdutor, 'agricultor_familiar_data'),
                        'fl_assistencia_tecnica' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_assistencia_tecnica')),
                        'assistencia_tecnica_tipo_id' => $this->formatAssistenciaTecnicaTipo($this->getValueColumn($rowData, $this->columnsProdutor, 'assistencia_tecnica_tipo_id')),
                        'assistencia_tecnica_periodo' => $this->getValueColumn($rowData, $this->columnsProdutor, 'assistencia_tecnica_periodo'),
                        'fl_comunidade_tradicional' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_comunidade_tradicional')),
                        'comunidade_tradicional_obs' => $this->getValueColumn($rowData, $this->columnsProdutor, 'comunidade_tradicional_obs'),
                        'fl_internet' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_internet')),
                        'fl_tipo_parceria' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_tipo_parceria')),
                        'tipo_parcerias_obs' => $this->getValueColumn($rowData, $this->columnsProdutor, 'tipo_parcerias_obs'),
                        'fl_reside_unidade_produtiva' => $this->formatSimNaoSemResposta($this->getValueColumn($rowData, $this->columnsProdutor, 'fl_reside_unidade_produtiva')),
                        'cep' => $this->formatCep($this->getValueColumn($rowData, $this->columnsProdutor, 'cep')),
                        'endereco' => $this->getValueColumn($rowData, $this->columnsProdutor, 'endereco'),
                        'bairro' => $this->getValueColumn($rowData, $this->columnsProdutor, 'bairro'),
                        'cidade_id' => $this->formatMunicipio($this->getValueColumn($rowData, $this->columnsProdutor, 'municipio')),
                        'estado_id' => $this->formatEstado($this->getValueColumn($rowData, $this->columnsProdutor, 'estado')),

                        'status' => $this->getStatusAtivoInativo($this->getValueColumn($rowData, $this->columnsProdutor, 'status')),
                        'status_observacao' => $this->getValueColumn($rowData, $this->columnsProdutor, 'status_observacao'),
                        'renda_agricultura_id' => $this->getValueColumnOrNull($rowData, $this->columnsProdutor, 'renda_agricultura_id'),
                        'rendimento_comercializacao_id' => $this->getValueColumnOrNull($rowData, $this->columnsProdutor, 'rendimento_comercializacao_id'),
                        'outras_fontes_renda' => $this->getValueColumn($rowData, $this->columnsProdutor, 'outras_fontes_renda'),
                        'grau_instrucao_id' => $this->getValueColumnOrNull($rowData, $this->columnsProdutor, 'grau_instrucao_id'),

                        'created_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsProdutor, 'created_at')),
                        'updated_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsProdutor, 'created_at')),
                        'user_id' => 1, # Solução provisória: atribuir id = 1 #10
                    ];

                    ProdutorModel::insert($dataProdutor);
                } catch (\Exception $e) {
                    // var_dump($dataProdutor['id']);
                    // var_dump($e->getMessage());

                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataProdutor['id'] . '] DUPLICADO - produtores';
                    } else {
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de produtores não corresponde ao template definido');
        }
    }

    protected function getStatusAtivoInativo($value)
    {
        $value = strtolower(trim($value));
        if ($value == 'inativo') {
            return 'inativo';
        } else {
            return 'ativo';
        }
    }

    protected function formatTelefone($value)
    {
        $value = str_replace(array("(", ")"), "", $value);
        $value = str_replace(array("-"), " ", $value);
        return $value;
    }

    protected function formatGenero($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        $value = strtolower($value);
        if ($value == 'feminino') {
            return 1;
        } else if ($value == 'outro') {
            return 3;
        } else if ($value == 'prefiro nao dizer' || $value == 'prefiro não dizer') {
            return 4;
        } else {
            return 2;
        }
    }

    protected function formatEtinia($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        $value = $this->removeAccents(strtolower($value));

        $values = array('parda', 'amarela', 'preta', 'indigina', 'branca', 'nao desejo informar');

        $pos = array_search($value, $values);
        if ($pos > -1) {
            return $pos;
        } else {
            return null;
        }
    }

    protected function formatDateString($date)
    {
        if ($date) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
            return  $date->format('Y-m-d');
        }

        return null;
    }

    protected function formatBoolean($value)
    {
        $value = strtolower($this->removeAccents($value));
        if ($value == 'sim' || $value == 's' || $value == '1') {
            return 1;
        } else {
            return 0;
        }
    }

    protected function formatOnlyNumbers($value)
    {
        $matches = array();
        preg_match_all('/\d+/', $value, $matches);

        $ret =  @join("", @$matches[0]);
        if (@$ret) {
            return $ret;
        }

        return null;
    }

    private $cacheAssistenciaTecnica;

    protected function formatAssistenciaTecnicaTipo($value)
    {
        if (!$value) {
            return null;
        }

        if (!@$this->cacheAssistenciaTecnica) {
            $cacheAssistenciaTecnica = AssistenciaTecnicaTipoModel::all()->pluck('nome', 'id');
            foreach ($cacheAssistenciaTecnica as $k => $v) {
                $cacheAssistenciaTecnica[$k] = trim(strtolower($this->removeAccents($v)));
            }
            $this->cacheAssistenciaTecnica = array_flip($cacheAssistenciaTecnica->toArray());
        }

        $values = explode(",", str_replace(".", ",", $value));

        if (count($values) == 0) {
            return null;
        }

        foreach ($values as $k => $v) {
            $values[$k] = trim(strtolower($this->removeAccents($v)));
        }

        return is_numeric($values[0]) ? $value[0] : null;

        // dd($this->cacheAssistenciaTecnica, $values);
        //Só retorna um registro o outro esta sendo ignorado
        return @$this->cacheAssistenciaTecnica[$values[0]];
    }

    protected function formatCep($value)
    {
        return $value;
    }

    private $cacheMunicipio = [];
    private $cacheEstado = [];

    protected function formatMunicipio($value)
    {
        $value = trim(strtolower($this->removeAccents($value)));

        if (@$this->cacheMunicipio[$value]) {
            return $this->cacheMunicipio[$value];
        } else {
            $cidade = CidadeModel::whereRaw('removeAccents(nome) = ?', [$value])->first();
            if ($cidade) {
                $this->cacheMunicipio[$value] = $cidade->id;
                return $cidade->id;
            } else {
                dd("notfound cidade", $value);
            }
        }
    }

    protected function formatEstado($value)
    {
        $value = trim(strtolower($this->removeAccents($value)));

        if (@$this->cacheEstado[$value]) {
            return $this->cacheEstado[$value];
        } else {
            $estado = EstadoModel::whereRaw('removeAccents(nome) = ?', [$value])->first();
            if ($estado) {
                $this->cacheEstado[$value] = $estado->id;
                return $estado->id;
            } else {
                dd("notfound estado", $value);
            }
        }
    }
}
