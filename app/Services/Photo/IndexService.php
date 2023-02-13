<?php

namespace App\Services\Photo;

use App\Models\Photo;
use Spatie\Fractalistic\Fractal;
use App\Services\Photo\Transformer\IndexTransformer;

class IndexService extends BaseCurrentService
{
    public function run()
    {
        $search = request()->get('search');
        $query = Photo::query();
        if ($search) $query->search($search);
        $columnSorts = [];
        $columnFilters = [];
        $columnFiltersExact = [];
        $resultPaging = $this->showResponsePaginate($query, $columnSorts, $columnFilters, $columnFiltersExact);
        $dataCollections = $resultPaging['data'];
        // For Fractal Transform
        $dataFractalled = Fractal::create()
            ->collection($dataCollections)
            ->transformWith(IndexTransformer::class)
            ->serializeWith(new \Spatie\Fractalistic\ArraySerializer())
            ->toArray();
        $resultPaging['data'] = $dataFractalled;
        // End Transform
        return $resultPaging;
    }
}
