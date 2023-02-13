<?php

namespace App\Services\MainTransaction;

use App\Models\MainTransaction;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Notifications\EmailVerification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\Fractalistic\Fractal;
use App\Services\MainTransaction\Transformer\IndexTransformer;

class IndexService extends BaseCurrentService
{
    public function run()
    {
        $search = request()->get('search');
        $query = MainTransaction::queryList();
        if ($search) $query->search($search);
        $columnSorts = ['status', 'type', 'stakeholder_id', 'cooperation_id'];
        $columnFilters = [AllowedFilter::scope('date_send_between'), AllowedFilter::scope('created_at_between')];
        $columnFiltersExact = ['stakeholder_id', 'status', 'type', 'sender.type', 'cooperation_id'];
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
