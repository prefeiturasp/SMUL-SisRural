<?php

namespace App\Logger;

use Illuminate\Support\Facades\Auth;

class SisruralRequestProcessor
{
    public function __construct()
    {
    }

    public function __invoke(array $record)
    {
        $user = Auth::user();

        if (!$user) {
            return $record;
        }

        $record['user_id'] = $user->id;
        $record['user_name'] = $user->fullName;

        $record['extra']['url'] = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        $record['extra']['url_params'] = join("&", [parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY), http_build_query($_POST)]);

        return $record;
    }
}
