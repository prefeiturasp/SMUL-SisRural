<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\Traits\ReportCadernoCampoTrait;
use App\Http\Controllers\Backend\Traits\ReportChecklistTrait;
use App\Http\Controllers\Backend\Traits\ReportPdaTrait;
use App\Http\Controllers\Backend\Traits\ReportUnidadeProdutivaInfraTrait;
use App\Http\Controllers\Backend\Traits\ReportUnidadeProdutivaPessoaTrait;
use App\Http\Controllers\Backend\Traits\ReportUnidadeProdutivaTrait;
use App\Http\Controllers\Backend\Traits\ReportUnidadeProdutivaUsoSoloTrait;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;

// ini_set('max_execution_time', 10); //3 minutes

class ReportController extends Controller
{
    use ReportUnidadeProdutivaTrait;
    use ReportUnidadeProdutivaPessoaTrait;
    use ReportUnidadeProdutivaInfraTrait;
    use ReportUnidadeProdutivaUsoSoloTrait;
    use ReportCadernoCampoTrait;
    use ReportChecklistTrait;
    use ReportPdaTrait;

    private $service;
    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $viewFilter = $this->service->viewFilter(null, false, true, true);

        return \Response::view('backend.core.report.index', ['viewFilter' => $viewFilter]);
    }

    //https://gist.github.com/mpijierro/2aae202292223292ee660ea76bbef871
    public function downloadCsv($filename, $query, $columns, $fnCsvHandle)
    {
        set_time_limit(0);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        if (request()->input('debugQuery') && config('app.debug')) {
            dd($query->get());
        }

        if (request()->input('debugCsv') && config('app.debug')) {
            $headers = [
                'Content-Type' => 'text/html',
            ];
        }

        if (request()->input('debugQueryLog') && config('app.debug')) {
            \DB::enableQueryLog();
            $value = $query->limit(1)->get();
            echo "<pre>" . print_r($value->toArray()) . "</pre>";
            echo "<pre>" . print_r(\DB::getQueryLog(), true) . "</pre>";
            die();
        }

        $response = new StreamedResponse(function () use ($columns, $query, $fnCsvHandle) {
            try {
                $handle = fopen('php://output', 'w');

                fwrite($handle, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

                fputcsv($handle, $columns, ';');

                $query->chunk(200, function ($list) use ($handle, $fnCsvHandle) {
                    foreach ($list as $v) {
                        // \Log::info(memory_get_usage());
                        $fnCsvHandle($handle, $v);
                    }
                });

                fclose($handle);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }, 200, $headers);

        return $response;
    }

    private function scapeInt($v)
    {
        if (@!$v) {
            return '';
        }

        return '"' . $v . '"';
    }

    private function removeBreakLine($data)
    {
        foreach ($data as $k => $v) {
            if ($v) {
                $data[$k] = preg_replace("/\r|\n/", "", nl2br($v));
            }
        }

        return $data;
    }

    private function filename($filename, $extension = '.csv')
    {
        return $filename . '_' . date('Y_m_d') . $extension;
    }

    private function privateData($value)
    {
        if ($this->service->isPrivateData()) {
            return null;
        }

        return $value;
    }

    // return $this->testDownload('caderno_arquivos/00a12f5d-cff7-4836-937a-3c6b053906a1.jpg', 'foo.jpg');
    private function testDownload($fileName, $newFilename)
    {
        return response()->stream(function () use ($fileName, $newFilename) {
            $stream = \Storage::readStream($fileName);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Cache-Control'         => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type'          => \Storage::mimeType($fileName),
            'Content-Length'        => \Storage::size($fileName),
            'Content-Disposition'   => 'attachment; filename="' . basename($newFilename) . '"',
            'Pragma'                => 'public',
        ]);
    }
}
