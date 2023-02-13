<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\Fractalistic\Fractal;
use App\Services\User\Transformer\IndexTransformer;

class IndexService extends BaseCurrentService
{
    public function run()
    {
        $search = request()->get('search');
        $typeDatas = ['produsen-migor', 'produsen-cpo', 'pengecer', 'distributor2', 'distributor','exportir'];
        $query = User::with('stakeholder')->whereIn('type', $typeDatas);
        if ($search) $query->search($search);
        $columnSorts = ['type'];
        $columnFilters = [
            AllowedFilter::scope('stakeholder_created_at_between')
        ];
        $columnFiltersExact = ['type'];
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
