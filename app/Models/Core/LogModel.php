<?php

namespace App\Models\Core;

use App\Models\Auth\User;
use App\Models\Core\Traits\Scope\LogsPermissionScope;
use App\Models\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;
use danielme85\LaravelLogToDB\Models\LogToDbCreateObject;

class LogModel extends Model
{
    use DateFormat;
    use LogToDbCreateObject;

    protected $table = 'log';

    //Insere os logs em outra estrutura de banco de dados
    protected $connection = 'mysql_logs';

    protected $fillable = ['message', 'channel', 'level', 'level_name', 'unix_time', 'datetime', 'context', 'extra', 'user_id', 'user_name'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new LogsPermissionScope);
    }

    public function generate(array $record)
    {
        if (isset($record['message'])) {
            $this->message = $record['message'];
        }
        if (!empty($record['context'])) {
            $this->context = $record['context'];
        }
        if (isset($record['level'])) {
            $this->level = $record['level'];
        }
        if (isset($record['level_name'])) {
            $this->level_name = $record['level_name'];
        }
        if (isset($record['channel'])) {
            $this->channel = $record['channel'];
        }
        if (isset($record['datetime'])) {
            $this->datetime = $record['datetime'];
        }
        if (!empty($record['extra'])) {
            $this->extra = $record['extra'];
        }
        $this->unix_time = time();

        //Custom
        if (isset($record['user_id'])) {
            $this->user_id = $record['user_id'];
        }
        if (isset($record['user_name'])) {
            $this->user_name = $record['user_name'];
        }

        return $this;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
