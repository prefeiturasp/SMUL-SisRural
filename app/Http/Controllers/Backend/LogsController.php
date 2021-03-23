<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\General\AppHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\LogModel;
use DataTables;

class LogsController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Listagem principal dos Logs
     *
     * @return void
     */
    public function index()
    {
        // \Log::channel('sisrural')->info("logs datatable");

        $datatableUrl = route('admin.core.logs.datatable');

        return view('backend.core.logs.index', compact('datatableUrl'));
    }

    /**
     * API Datatable "index()"
     *
     * Listagem principal dos Logs - Retorno dos dados p/ consumo - DataTable
     *
     * @return mixed
     */
    public function datatable()
    {
        $data = LogModel::query();

        return DataTables::of($data)
            ->editColumn('extra', function ($row) {
                if (@!$row->extra) {
                    return null;
                }

                return join("<br>", array_map(function ($v, $k) {
                    if ($k == 'url_params') {
                        $list = array();
                        parse_str($v, $list);

                        $v = pp($list);
                    }

                    return '<b>' . $k . '</b>: ' . $v;
                }, $row->extra, array_keys($row->extra)));
            })
            ->addColumn('created_at_formatted', function ($row) {
                return $row->created_at_formatted;
            })
            ->rawColumns(['extra'])
            ->make(true);
    }
}

function pp($arr)
{
    $retStr = '<ul>';
    if (is_array($arr)) {
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $retStr .= '<li>' . $key . ' => ' . pp($val) . '</li>';
            } else {
                $retStr .= '<li>' . $key . ' => ' . $val . '</li>';
            }
        }
    }
    $retStr .= '</ul>';
    return $retStr;
}
