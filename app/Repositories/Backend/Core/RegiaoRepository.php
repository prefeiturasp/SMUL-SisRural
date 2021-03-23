<?php

namespace App\Repositories\Backend\Core;

use App\Exceptions\GeneralException;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Core\RegiaoModel;
use Geo;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;

class RegiaoRepository extends BaseRepository
{

    public function __construct(RegiaoModel $model)
    {
        $this->model = $model;
    }

    public function create(array $data): RegiaoModel
    {
        return DB::transaction(function () use ($data) {
            $data['poligono'] = $this->getMultiPolygon($data['poligono']);
            $model = $this->model::create($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function update(RegiaoModel $model, array $data): RegiaoModel
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update($data);

            if ($model) {
                return $model;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function delete(RegiaoModel $model): bool
    {
        return DB::transaction(function () use ($model) {
            if ($model->delete()) {
                return true;
            }

            throw new GeneralException('Favor tentar novamente');
        });
    }

    public function getMultiPolygon($file)
    {
        $kml = Geo::parseKml(file_get_contents($file->getRealPath()));
        return MultiPolygon::fromWKT($kml->toWkt());
    }
}
