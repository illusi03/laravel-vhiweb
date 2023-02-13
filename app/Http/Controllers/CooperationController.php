<?php

namespace App\Http\Controllers;

use App\Services\Cooperation\IndexService;

class CooperationController extends Controller
{
    private $indexService;

    function __construct(
        IndexService $indexService
    ) {
        $this->indexService = $indexService;
    }

    public function index()
    {
        return $this->indexService->run();
    }
}
