<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Enums\CadernoStatusEnum;
use App\Enums\TipoTemplatePerguntaEnum;
use App\Models\Core\CadernoArquivoModel;
use App\Models\Core\CadernoModel;
use App\Models\Core\CadernoRespostaCadernoModel;
use App\Models\Core\TemplatePerguntaModel;
use Carbon\Carbon;

trait ImportadorCadernoCampoTrait
{
    private $columnsCadernoCampo = array(
        array("id" => "id", "column" => null),
        array("id" => "created_at", "column" => null),
        array("id" => "finished_at", "column" => null),
        array("id" => "user_id", "column" => null),
        array("id" => "unidade_produtiva_id", "column" => null),
        array("id" => "produtor_id", "column" => null),
    );

    private function importCadernoCampo($spreadsheet, $numberSheet)
    {
        if (!$this->isMailDriverLog()) {
            dd("Não é possível executar a importação com o 'mail_driver' != 'log'");
        }

        //Template Fixo para o Domínio ATER
        $templateId = 1;

        $defaultCollumnsToCheck = $this->columnsCadernoCampo;

        $perguntas = TemplatePerguntaModel::all();
        foreach ($perguntas as $k => $v) {
            array_push($this->columnsCadernoCampo, array('id' => 'template_pergunta_' . $v->id, 'column' => null));
        }

        $totalFotos = 25;
        for ($i = 1; $i <= $totalFotos; $i++) {
            array_push($this->columnsCadernoCampo, array('id' => 'foto_' . $i, 'column' => null));
            array_push($this->columnsCadernoCampo, array('id' => 'foto_descricao_' . $i, 'column' => null));
        }

        $sheet = $spreadsheet->getSheet($numberSheet);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $columns = $this->getColumnsIds($sheet, $highestColumn, 2);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsCadernoCampo = $this->prepareFillColumns($this->columnsCadernoCampo, $columns);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($defaultCollumnsToCheck, $columns)) {

            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                $idCaderno = $this->getValueColumn($rowData, $this->columnsCadernoCampo, 'id');

                if (!$idCaderno) {
                    continue;
                }

                //Caderno
                try {
                    $dataCaderno = [
                        'id' => $idCaderno,
                        'uid' => $idCaderno,
                        'template_id' => $templateId,
                        'produtor_id' => $this->getValueColumn($rowData, $this->columnsCadernoCampo, 'produtor_id'),
                        'unidade_produtiva_id' => $this->getValueColumn($rowData, $this->columnsCadernoCampo, 'unidade_produtiva_id'),
                        'user_id' => $this->getValueColumn($rowData, $this->columnsCadernoCampo, 'user_id'),
                        'status' => CadernoStatusEnum::Finalizado,
                        'created_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsCadernoCampo, 'created_at')),
                        'finished_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsCadernoCampo, 'finished_at')),
                        'updated_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsCadernoCampo, 'created_at'))
                    ];

                    CadernoModel::create($dataCaderno);
                } catch (\Exception $e) {
                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataCaderno['id'] . '] DUPLICADO - cadernos';
                    } else {
                        throw $e;
                    }
                }

                //Respostas do Template
                CadernoRespostaCadernoModel::where('caderno_id', $idCaderno)->forceDelete();

                $createdAt = Carbon::now();
                foreach ($perguntas as $k => $v) {
                    $value = $this->getValueColumn($rowData, $this->columnsCadernoCampo, 'template_pergunta_' . $v->id);

                    if ($value) {
                        //Campo data
                        if ($v->id == 1) {
                            $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('d/m/Y');
                        }

                        $dataResposta = [
                            'caderno_id' => $idCaderno,
                            'template_pergunta_id' => $v->id,
                            'template_resposta_id' => $v->tipo == TipoTemplatePerguntaEnum::Check ? $value : null,
                            'resposta' => $v->tipo == TipoTemplatePerguntaEnum::Text ? $value : null,
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt
                        ];

                        if ($v->tipo == TipoTemplatePerguntaEnum::MultipleCheck) {
                            $values = explode(",", str_replace(".", ",", $value));
                            foreach ($values as $k => $v) {
                                $dataResposta['template_resposta_id'] = $v;
                                CadernoRespostaCadernoModel::create($dataResposta);
                            }
                        } else {
                            CadernoRespostaCadernoModel::create($dataResposta);
                        }
                    }
                }

                //Assinatura
                CadernoArquivoModel::where('caderno_id', $idCaderno)->forceDelete();
                $assinatura = $this->getValueColumn($rowData, $this->columnsCadernoCampo, 'assinatura');
                if (@$assinatura) {
                    $dataArquivo = [
                        'caderno_id' => $idCaderno,
                        'arquivo' => 'caderno_arquivos/' . $assinatura,
                        'descricao' => 'Assinatura',
                        'tipo' => $this->getTipoArquivo($assinatura),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'nome' => $assinatura
                    ];

                    CadernoArquivoModel::create($dataArquivo);
                }

                //Arquivos
                for ($i = 1; $i <= $totalFotos; $i++) {
                    $foto = $this->getValueColumn($rowData, $this->columnsCadernoCampo, 'foto_' . $i);
                    if (@$foto) {
                        $descricao = $this->getValueColumn($rowData, $this->columnsCadernoCampo, 'foto_descricao_' . $i);

                        $dataArquivo = [
                            'caderno_id' => $idCaderno,
                            'arquivo' => 'caderno_arquivos/' . $foto,
                            'descricao' => $descricao,
                            'tipo' => $this->getTipoArquivo($foto),
                            'created_at' => $createdAt,
                            'updated_at' => $createdAt,
                            'nome' => $foto
                        ];

                        CadernoArquivoModel::create($dataArquivo);
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela de Caderno de Campo não corresponde ao template definido');
        }
    }
    public function getTipoArquivo($value)
    {
        $explode = @explode('.', strtolower($value));
        $extension = array_pop($explode);

        if (@in_array($extension, ['png', 'gif', 'jpg', 'jpeg']))
            return 'imagem';
        else
            return 'arquivo';
    }

    public function formatDateTimezoneCaderno($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
