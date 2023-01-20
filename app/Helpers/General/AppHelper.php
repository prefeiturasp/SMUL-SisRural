<?php

namespace App\Helpers\General;

use App\Models\Auth\Traits\Scope\UserPermissionScope;
use App\Models\Auth\User;
use Auth;
use Carbon\Carbon;

class AppHelper
{
    /**
     * Gera uma key conforme os parametros enviados
     *
     * @return string
     */
    public static function getCacheKey() {
        $list = func_get_args();

        $ret = '';
        foreach ($list as $k=>$v) {
            if (!$v) {
                $v = [];
            }

            if (is_string($v)) {
                $v = [$v];
            }

            $ret .= $k.'_'.http_build_query($v);
        }

        return $ret;
    }


    /**
     * Retorna em segundos o tempo de execução
     *
     * Deve ser habilitado o "\DB::enableQueryLog();"
     *
     * @return string
     */
    public static function debugQueryLogTime()
    {
        $time = array_reduce(\DB::getQueryLog(), function($carry, $item) {
            $carry += $item['time'];
            return $carry;
        }, 0)/ 1000;

        return $time.' seconds';
    }

    /**
     * Formata a data
     *
     * @param  mixed $strDate
     * @param  mixed $format
     * @return string
     *
     */
    public static function formatDate($strDate, $format = 'd/m/Y H:i:s')
    {
        if (!$strDate) {
            return null;
        }

        return Carbon::parse($strDate)->setTimezone(('America/Sao_Paulo'))->format($format);
    }

    /**
     * Formata a data sem locale
     *
     * @param  mixed $strDate
     * @param  mixed $format
     * @return string
     *
     */
    public static function formatDateUtc($strDate, $format = 'd/m/Y')
    {
        return Carbon::parse($strDate)->format($format);
    }

    /**
     * Retorna a url anterior caso exista.
     *
     * Caso não tenha, retorna a defaultUrl passada
     *
     * Essa função é utilizada para os botões de "voltar" das páginas (caderno de campo, formulários aplicados, plano de ação ... ) (view, edit, create ...)
     *
     * @param  string $defaultUrl
     * @return string
     */
    public static function prevUrl($defaultUrl)
    {
        $backUrl = redirect()->back()->getTargetUrl();
        $currentUrl = url()->current();

        //Previne entrar direto na url e não ter "url" para voltar. url()->previous() retorna a última url gerada pelo sistema, não é a mesma coisa que o redirect()->back()
        if ($backUrl && $backUrl !== $currentUrl) {
            return redirect()->back()->getTargetUrl();
        } else {
            return $defaultUrl;
        }
    }

    /**
     * Formatação (pontuações) do valor em CPF ou CNPJ
     *
     * $value = '00000000000', return 000.000.000-00
     *
     * @param  mixed $value
     * @return string
     */
    public static function formatCpfCnpj($value)
    {
        $cnpj_cpf = preg_replace("/\D/", '', $value);

        if (strlen($cnpj_cpf) === 11) {
            return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
        }

        return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }

    /**
     * Converte um Array em uma lista <ol<ul (com um field especificado)
     *
     * @param  array $data
     * @param  string $field
     * @return string
     */
    public static function tableArrayToList($data, $field)
    {
        return '<ol style="padding-inline-start:15px;">' . join("", array_map(
            function ($value) use ($field) {
                return '<li>' . $value[$field] . '</li>';
            },
            $data
        )) . '</ol>';
    }

    /**
     * Converte um valor em percentual.
     *
     * value = 5, total = 10, return 50%;
     *
     * @param  double $value
     * @param  double $max
     * @return string
     */
    public static function toPerc($value, $max)
    {
        if (!$max) {
            return '';
        }

        return '(' . round(($value / $max) * 100) . '%)';
    }

    /**
     * Converte uma lista em um componente expandido, para ser consumido dentro do DataTable
     *
     * @param  mixed $data
     * @param  mixed $field
     * @param  mixed $label
     * @param  mixed $limit
     * @return string
     */
    public static function tableArrayToListExpand($data, $field = null, $label = 'Ver mais', $limit = 3)
    {
        $count = 0;

        $notExpand = '<div>' . join("", array_map(
            function ($value) use ($field, &$count) {
                $count += 1;
                return '<div>' . $count . '. ' . ($field ? $value[$field] : $value) . '</div>';
            },
            array_slice($data, 0, $limit)
        )) . '</div>';

        if (count($data) > $limit) {
            $expand = '<div class="collapse">' . join("", array_map(
                function ($value) use ($field, &$count) {
                    $count += 1;
                    return '<div>' . $count . '. ' . ($field ? $value[$field] : $value)  . '</div>';
                },
                array_slice($data, $limit, count($data))
            )) . '</div>';

            $btnExpand = '<div class="btn btn-outline-primary btn-sm btn-collapse-ater mt-2">' . $label . '</div>';
        }

        return $notExpand . @$expand . @$btnExpand;
    }

    /**
     * Retorna um array de valores no formato de <tags/>
     *
     * @param  mixed $data
     * @return void
     */
    public static function tableTags($data)
    {
        if (!$data) {
            return '';
        }

        return join("", array_map(function ($value) {
            return '<h5 class="d-inline"><span class="badge badge-light px-3 font-weight-normal mr-1">' . $value . '</span></h5>';
        }, explode(',', $data)));
    }

    /**
     * Retorna data formatada com time
     *
     * @param string $dt_ini
     * @param mixed $dt_end
     *
     * @return array
     */
    public static function dateBetween($dt_ini, $dt_end)
    {
        return [$dt_ini . " 00:00:00", $dt_end . " 23:59:59"];
    }

    /**
     * Retorna usuário autenticado ou da sessão
     */
    public static function getSessionOrAuthUser()
    {
        if (!session('auth_user_id')) {
            return Auth::user();
        }

        return \Cache::store('array')->remember('AppHelper-getSessionOrAuthUser-' . session('auth_user_id'), 60, function () {
            $user = User::withoutGlobalScope(UserPermissionScope::class)->findOrFail(session('auth_user_id'));
            return $user;
        });
    }

    /**
     * @param mixed $columns
     * @param mixed $lines
     * @param mixed $values
     *
     * @return [type]
     */
    public static function getDataTable($columns, $lines, $values)
    {
        $str = '<table class="table-sm" width="100%">';
        $str .= '<tr>';

        if ($lines && count($lines) > 0) {
            $str .= '<th>' . $lines[0] . '</th>';
        }

        $str .= '<th>' . join('</th><th>', $columns) . '</th>';
        $str .= '</tr>';

        foreach ($values as $k => $v) {
            if (trim(join("", $v)) != "" || count($lines) > 0) { //ignora linhas em branco ... mas se tiver linhas, não ignora
                $str .= '<tr>';

                if (@count($lines) > 0 && @$lines[$k + 1]) {
                    $str .= '<td class="font-weight-bold">' . $lines[$k + 1] . '</td>';
                }

                foreach ($columns as $kk => $vv) {
                    $str .= '<td>' . @$v[$vv] . '</td>';
                }

                $str .= '</tr>';
            }
        }

        $str .= '</table>';

        return $str;
    }

    /**
     * @param mixed $array
     *
     * @return [type]
     */
    public static function transpose($array)
    {
        $keys = array_keys($array);
        return array_map(function ($array) use ($keys) {
            return array_combine($keys, $array);
        }, array_map(null, ...array_values($array)));
    }

    /**
     * Retorna em bytes a saída da variável upload_max_size do PHP
     *
     * @param string $val     
     *
     * @return int
     */
    public static function return_bytes($val)
    {    
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        $val = trim($val, 'MGK');
        
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= (1024 * 1024 * 1024); //1073741824
                break;
            case 'm':
                $val *= (1024 * 1024); //1048576
                break;
            case 'k':
                $val *= 1024;
                break;
        }

        return $val;    
    }
    
}
