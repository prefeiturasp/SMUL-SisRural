<?php

namespace App\Http\Controllers\Backend\Traits\Importador;

use App\Enums\ChecklistStatusEnum;
use App\Models\Core\ChecklistSnapshotRespostaModel;
use App\Models\Core\ChecklistUnidadeProdutivaModel;
use App\Models\Core\UnidadeProdutivaRespostaModel;

trait ImportadorChecklistUnidadeProdutivaTrait
{
    private $columnsChecklists = array(
        array("id" => "id", "column" => null),
        array("id" => "checklist_id", "column" => null),
        array("id" => "unidade_produtiva_id", "column" => null),
        array("id" => "produtor_id", "column" => null),
        array("id" => "user_id", "column" => null),
        array("id" => "created_at", "column" => null),
    );

    private $columnsChecklistsRespostas = array(
        array("id" => "id", "column" => null),
        array("id" => "checklist_unidade_produtiva_id", "column" => null),
        array("id" => "pergunta_id", "column" => null),
        array("id" => "resposta_id", "column" => null),
        array("id" => "created_at", "column" => null),
    );

    private function importChecklistUnidadeProdutivaFormularios($spreadsheet, $numberSheet)
    {
        if (!$this->isMailDriverLog()) {
            dd("Não é possível executar a importação com o 'mail_driver' != 'log'");
        }

        $defaultCollumnsToCheck = $this->columnsChecklists;

        $sheet = $spreadsheet->getSheet($numberSheet);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $columns = $this->getColumnsIds($sheet, $highestColumn, 2);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsChecklists = $this->prepareFillColumns($this->columnsChecklists, $columns);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($defaultCollumnsToCheck, $columns)) {

            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                $idChecklistUnidadeProdutiva = $this->getValueColumn($rowData, $this->columnsChecklists, 'id');

                if (!$idChecklistUnidadeProdutiva) {
                    continue;
                }

                try {
                    $dataChecklistUnidProdutiva = [
                        'id' => $idChecklistUnidadeProdutiva,
                        'uid' => $idChecklistUnidadeProdutiva,
                        'checklist_id' => $this->getValueColumn($rowData, $this->columnsChecklists, 'checklist_id'),
                        'unidade_produtiva_id' => $this->getValueColumn($rowData, $this->columnsChecklists, 'unidade_produtiva_id'),
                        'produtor_id' => $this->getValueColumn($rowData, $this->columnsChecklists, 'produtor_id'),
                        'user_id' => $this->getValueColumn($rowData, $this->columnsChecklists, 'user_id'),
                        'status' => ChecklistStatusEnum::Finalizado,
                        'created_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsChecklists, 'created_at')),
                        'updated_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsChecklists, 'created_at'))
                    ];

                    ChecklistUnidadeProdutivaModel::create($dataChecklistUnidProdutiva);
                } catch (\Exception $e) {
                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataChecklistUnidProdutiva['id'] . '] DUPLICADO - checklist_unidade_produtivas';
                    } else {
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela do Checklist x Unidade Produtiva não corresponde ao template definido');
        }
    }

    private function importChecklistUnidadeProdutivaRespostas($spreadsheet, $numberSheet)
    {
        $defaultCollumnsToCheck = $this->columnsChecklistsRespostas;

        $sheet = $spreadsheet->getSheet($numberSheet);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $columns = $this->getColumnsIds($sheet, $highestColumn, 2);

        //Seta as posições das colunas baseadas no Excel vs Colunas Defaults
        $this->columnsChecklistsRespostas = $this->prepareFillColumns($this->columnsChecklistsRespostas, $columns);

        //Valida para ver se todas as colunas necessárias estão presentes no Excel
        $errors = array();
        if ($this->validColumns($defaultCollumnsToCheck, $columns)) {
            //Inicia a inserção dos dados, inicia na linha 4
            for ($row = 4; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE)[0];

                $id = $this->getValueColumn($rowData, $this->columnsChecklistsRespostas, 'id');

                if (!$id) {
                    continue;
                }

                try {
                    $dataResposta = [
                        'id' => $id,
                        'uid' => $id,
                        'checklist_unidade_produtiva_id' => $this->getValueColumn($rowData, $this->columnsChecklistsRespostas, 'checklist_unidade_produtiva_id'),
                        'pergunta_id' => $this->getValueColumn($rowData, $this->columnsChecklistsRespostas, 'pergunta_id'),
                        'resposta_id' => $this->getValueColumn($rowData, $this->columnsChecklistsRespostas, 'resposta_id'),
                        // 'resposta' => $this->getValueColumn($rowData, $this->columnsChecklistsRespostas, 'resposta'),
                        'created_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsChecklistsRespostas, 'created_at')),
                        'updated_at' => $this->formatDateTimezoneCaderno($this->getValueColumn($rowData, $this->columnsChecklistsRespostas, 'created_at'))
                    ];

                    ChecklistSnapshotRespostaModel::create($dataResposta);

                    $checklistUnidadeProdutiva = ChecklistUnidadeProdutivaModel::where("id", $dataResposta['checklist_unidade_produtiva_id'])->first();
                    UnidadeProdutivaRespostaModel::updateOrCreate(['unidade_produtiva_id' => $checklistUnidadeProdutiva->unidade_produtiva_id, 'pergunta_id' => $dataResposta['pergunta_id']], ['resposta_id' => $dataResposta['resposta_id'], 'resposta' => @$dataResposta['resposta']]);
                } catch (\Exception $e) {
                    //Se não for duplicado, dispara o erro para analisar a importação
                    $MYSQL_DUPLICATE_CODES = array(1062, 23000);
                    if (in_array($e->getCode(), $MYSQL_DUPLICATE_CODES)) {
                        $errors[] = '[' . $dataResposta['id'] . '] DUPLICADO - checklist_snapshot_respostas';
                    } else {
                        throw $e;
                    }
                }
            }

            return $errors;
        } else {
            dd('O formato da tabela do Checklist x Unidade Produtiva x Respostas não corresponde ao template definido');
        }
    }
}
