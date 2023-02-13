<?php

namespace App\Services\Cooperation;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\Fractalistic\Fractal;
use App\Services\Cooperation\Transformer\IndexTransformer;

class IndexService extends BaseCurrentService
{
    public function run()
    {
        $search = request()->get('search');
        $query = Cooperation::queryList();
        if ($search) $query->search($search);
        $columnSorts = [];
        $columnFilters = ['doc_no', 'measurement'];
        $columnFiltersExact = ['user_id', 'stakeholder_id'];
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
