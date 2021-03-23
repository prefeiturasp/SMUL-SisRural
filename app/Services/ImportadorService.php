<?php

namespace App\Services;

use App\Http\Controllers\Backend\Traits\Importador\ImportadorCadernoCampoTrait;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorChecklistUnidadeProdutivaTrait;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorInfraEstruturaTrait;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorPessoasTrait;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorProdutorTrait;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorProdutorUnidadeProdutiva;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorUnidadesProdutivas;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorUpdateProdutorTrait;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorUpdateUnidadesProdutivasTrait;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorUsoSoloTrait;
use App\Http\Controllers\Backend\Traits\Importador\ImportadorUsuariosTrait;
use App\Models\Core\UnidadeOperacionalModel;

class ImportadorService
{
    use ImportadorProdutorTrait;
    use ImportadorUnidadesProdutivas;
    use ImportadorProdutorUnidadeProdutiva;
    use ImportadorPessoasTrait;
    use ImportadorInfraEstruturaTrait;
    use ImportadorUsoSoloTrait;

    use ImportadorCadernoCampoTrait;
    use ImportadorChecklistUnidadeProdutivaTrait;
    use ImportadorUsuariosTrait;

    use ImportadorUpdateProdutorTrait;
    use ImportadorUpdateUnidadesProdutivasTrait;

    protected $service;

    public function __construct(UnidadeOperacionalService $service)
    {
        \Config::set('import_service', true);

        $this->service = $service;
    }

    public function importUsuarios($pathname)
    {
        set_time_limit(-1);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($pathname);

        $errors = $this->importUsuario($spreadsheet, 0);

        if (count($errors) > 0) {
            dd($errors);
        }
    }

    public function importChecklistUnidadeProdutiva($pathname)
    {
        set_time_limit(-1);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($pathname);

        $errors = $this->importChecklistUnidadeProdutivaFormularios($spreadsheet, 1);

        $errorsRespostas = $this->importChecklistUnidadeProdutivaRespostas($spreadsheet, 2);

        if (count($errors) > 0 || count($errorsRespostas) > 0) {
            dd($errors, $errorsRespostas);
        }
    }

    public function importCaderno($pathname)
    {
        set_time_limit(-1);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($pathname);

        $errors = $this->importCadernoCampo($spreadsheet, 1);

        if (count($errors) > 0) {
            dd($errors);
        }
    }

    public function importUpdateProdutor($pathname)
    {
        set_time_limit(-1);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($pathname);

        $errors = collect();

        //PRODUTORES
        $errors[] = $this->importUpdateProdutores($spreadsheet, 1);

        //UNIDADES PRODUTIVAS
        $errors[] = $this->importUpdateUnidadesProdutivas($spreadsheet, 2);

        $errors = $errors->flatten()->toArray();

        if (count($errors) > 0) {
            dd("errors gerais", $errors);
        }
    }

    public function import($pathname)
    {
        set_time_limit(-1);

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($pathname);

        $errors = collect();

        //PRODUTORES
        $errors[] = $this->importProdutores($spreadsheet, 1);

        //UNIDADES PRODUTIVAS
        $errors[] = $this->importUnidadesProdutivas($spreadsheet, 2);

        //PRODUTOR VS UNIDADE PRODUTIVA
        $errors[] = $this->importProdutoresUnidadesProdutivas($spreadsheet, 3);

        //UNIDADE PRODUTIVA - PESSOAS
        $errors[] = $this->importPessoas($spreadsheet, 4);

        //UNIDADE PRODUTIVA - INFRA ESTRUTURA
        $errors[] = $this->importInfraEstrutura($spreadsheet, 5);

        //UNIDADE PRODUTIVA - USO DO SOLO
        $errors[] = $this->importUsoSolo($spreadsheet, 6);

        //Sync Abrangencias
        $unidadesOperacionais = UnidadeOperacionalModel::all();
        foreach ($unidadesOperacionais as $k => $v) {
            $this->service->syncAbrangencias($v);
        }

        //  Verifica se existe algum produtor que não tem relacionamento com a tabela "produtor_unidade_produtiva"
        $rsProdutores = \DB::select(\DB::raw("SELECT * FROM produtores WHERE id NOT IN (SELECT produtor_id FROM produtor_unidade_produtiva)"));

        // Verifica se existe alguma unidade produtiva sem relacionamento com a tabela "produtor_unidade_produtiva"
        $rsUnidProd = \DB::select(\DB::raw("SELECT * FROM unidade_produtivas WHERE id NOT IN (SELECT unidade_produtiva_id FROM produtor_unidade_produtiva)"));

        //Verifica se existe alguma unidade produtiva sem unidade operacional
        $rsUnidProdUnidOpe = \DB::select(\DB::raw("SELECT * FROM unidade_produtivas WHERE id NOT IN (SELECT unidade_produtiva_id FROM unidade_operacional_unidade_produtiva)"));


        /**
         * # Verifica se existe algum produtor que não tem relacionamento com a tabela "produtor_unidade_produtiva"
         * select * from produtores where id not in (select produtor_id from produtor_unidade_produtiva);
         *
         * # Verifica se existe alguma unidade produtiva sem relacionamento com a tabela "produtor_unidade_produtiva"
         * select * from unidade_produtivas where id not in (select unidade_produtiva_id from produtor_unidade_produtiva);
         *
         * #Verifica se existe alguma unidade produtiva sem unidade operacional
         * select * from unidade_produtivas where id not in (select unidade_produtiva_id from unidade_operacional_unidade_produtiva);
         */

        $errors = $errors->flatten()->toArray();
        if (count($errors) > 0 || count($rsProdutores) > 0 || count($rsUnidProd) > 0 || count($rsUnidProdUnidOpe) > 0) {
            dd("errors gerais", $errors, 'produtores sem relacionamento com "produtor_unidade_produtiva"', $rsProdutores, 'unidades produtivas sem relacionamento com "produtor_unidade_produtiva"', $rsUnidProd, 'unidades produtivas sem relacionamento com "unidade_operacional_unidade_produtiva"', $rsUnidProdUnidOpe);
        }
    }


    protected function getColumnsIds($sheet, $highestColumn, $position = 2)
    {
        $columns = $sheet->rangeToArray('A' . $position . ':' . $highestColumn . $position, NULL, TRUE, FALSE)[0];

        foreach ($columns as $k => $v) {
            $columns[$k] = trim($v);
        }

        return $columns;
    }

    protected function validColumns($defaultColumns, $checkColumns)
    {
        $count = 0;

        foreach ($defaultColumns as $k => $v) {
            if (array_search($v['id'], $checkColumns) > -1) {
                $count += 1;
            } else {
                // dd($v['id'], $checkColumns);
            }
        }

        return count($defaultColumns) == $count;
    }

    private function prepareFillColumns($defaultColumns, $checkColumns)
    {
        foreach ($defaultColumns as $k => &$v) {
            $position = array_search($v['id'], $checkColumns);
            if ($position !== FALSE) {
                $v['column'] = $position;
            }
        }

        return $defaultColumns;
    }

    protected function getPosColumn($columns, $id)
    {
        foreach ($columns as $k => $v) {
            if ($v['id'] === $id) {
                return $v['column'];
            }
        }

        return null;
    }

    protected function getValueColumn($rowData, $defaultColumns, $field)
    {
        return @$rowData[$this->getPosColumn($defaultColumns, $field)];
    }

    protected function getValueColumnOrNull($rowData, $defaultColumns, $field)
    {
        $value = $this->getValueColumn($rowData, $defaultColumns, $field);
        if (!$value) {
            return null;
        }

        return $value;
    }

    protected function removeAccents($string)
    {
        $search = ':@;@.@,@?@¿@/@-@!@á@é@í@ó@ú@à@è@ì@ò@ù@ã@õ@â@ê@î@ô@ô@ä@ë@ï@ö@ü@ç@Á@É@Í@Ó@Ú@À@È@Ì@Ò@Ù@Ã@Õ@Â@Ê@Î@Ô@Û@Ä@Ë@Ï@Ö@Ü@Ç@"@\'';
        $replace = '         aeiouaeiouaoaeiooaeioucAEIOUAEIOUAOAEIOOAEIOUC  ';

        $arr = explode('@', $search);

        for ($i = 0; $i < count($arr); $i++) {
            $string = @str_replace($arr[$i], $replace[$i], $string);
        }

        return $string;
    }

    protected function isMailDriverLog()
    {
        return env('MAIL_DRIVER') === 'log';
    }
}
