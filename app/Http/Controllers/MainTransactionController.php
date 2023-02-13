<?php

namespace App\Http\Controllers;

use App\Services\MainTransaction\IndexService;
use App\Services\MainTransaction\StoreService;

class MainTransactionController extends Controller
{
    private $indexService;
    private $storeService;

    function __construct(
        IndexService $indexService,
        StoreService $storeService
    ) {
        $this->indexService = $indexService;
        $this->storeService = $storeService;
    }

    public function index()
    {
        return $this->indexService->run();
    }

    public function store()
    {
        request()->validate([
            'stakeholder_id' => 'required|exists:stakeholders,id,deleted_at,NULL',
            'publish' => 'nullable|date_format:Y-m-d',
            'date_send' => 'nullable|date_format:Y-m-d',
            'time_send' => 'nullable|date_format:H:i',
            'date_received' => 'nullable|date_format:Y-m-d',
            'is_full' => 'nullable|boolean',
            'parent_id' => 'nullable|string',
            'volume' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'volume_received' => 'nullable|numeric',
            'volume_conversion' => 'nullable|numeric',
        ]);
        return $this->storeService->run();
    }
}
