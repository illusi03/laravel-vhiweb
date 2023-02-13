<?php

namespace App\Services\User;

use App\Models\User;
use Spatie\Fractalistic\Fractal;
use App\Services\User\Transformer\IndexTransformer;

class IndexService extends BaseCurrentService
{
    public function run()
    {
        $search = request()->get('search');
        $query = User::query();
        if ($search) $query->search($search);
        $columnSorts = [];
        $columnFilters = [
            // AllowedFilter::scope('stakeholder_created_at_between')
        ];
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
