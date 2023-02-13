<?php

namespace App\Http\Controllers;

use App\Services\Photo\ShowService;
use App\Services\Photo\IndexService;
use App\Services\Photo\StoreService;
use App\Services\Photo\UpdateService;
use App\Services\Photo\DeleteService;
use App\Services\Photo\LikeService;
use App\Services\Photo\UnlikeService;

class PhotoController extends Controller
{
    private $indexService;
    private $showService;
    private $storeService;
    private $updateService;
    private $deleteService;
    private $likeService;
    private $unlikeService;

    function __construct(
        IndexService $indexService,
        ShowService $showService,
        StoreService $storeService,
        UpdateService $updateService,
        DeleteService $deleteService,
        LikeService $likeService,
        UnlikeService $unlikeService
    ) {
        $this->indexService = $indexService;
        $this->showService = $showService;
        $this->storeService = $storeService;
        $this->updateService = $updateService;
        $this->deleteService = $deleteService;
        $this->likeService = $likeService;
        $this->unlikeService = $unlikeService;
    }

    public function index()
    {
        return $this->indexService->run();
    }

    public function show($id)
    {
        return $this->showService->run($id);
    }

    public function store()
    {
        request()->validate([
            'caption' => 'string|required',
            'image' => [
                'required',
                'image',
                'mimes:jpg,png,jpeg,gif,svg',
                'max:10000',
                'nullable'
            ]
        ]);
        return $this->storeService->run();
    }

    public function update($id)
    {
        request()->validate([
            'caption' => 'string',
            'image' => [
                'image',
                'mimes:jpg,png,jpeg,gif,svg',
                'max:10000',
                'nullable'
            ]
        ]);
        return $this->updateService->run($id);
    }

    public function delete($id)
    {
        return $this->deleteService->run($id);
    }

    public function like($id)
    {
        return $this->likeService->run($id);
    }

    public function unlike($id)
    {
        return $this->unlikeService->run($id);
    }
}
