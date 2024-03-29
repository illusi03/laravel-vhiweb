<?php

namespace App\Services;

use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Traits\UploadTrait;
use App\Traits\UserTraits;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class BaseService
{
    use UserTraits, UploadTrait;

    private function setQueryBuilder($modelClass,  $columnSorts, $columnFilters, $columnFiltersExact)
    {
        $mapperExactField = function ($item) {
            return AllowedFilter::exact($item);
        };
        $paginate = request('limit') ?? 25;
        $columnSortsMust = ['id', 'created_at', 'updated_at'];
        $columnSorts = array_merge($columnSortsMust, $columnSorts);
        $columnFiltersExactMust = ['id', 'created_by', 'updated_by'];
        $columnFiltersExact = array_merge($columnFiltersExactMust, $columnFiltersExact);
        $columnFiltersExact =  array_map($mapperExactField, $columnFiltersExact);
        // Join All Filter
        $columnFilters = array_merge($columnFilters, $columnFiltersExact);
        $queryBuilder = QueryBuilder::for($modelClass);
        $resultQB = $queryBuilder
            ->allowedSorts($columnSorts)
            ->allowedFilters($columnFilters)
            ->paginate($paginate)
            ->appends(request()->query());
        return $resultQB;
    }

    protected function showResponse($data, $message = null)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message ?? 'success',
            'data' => $data,
        ], 200);
    }

    protected function showResponseUnauth($message = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => 'not authorize permissions',
        ], 401);
    }

    protected function showResponseNotFound()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'data not found',
            'data' => 'data not found',
        ], 400);
    }

    protected function showResponseError($message)
    {
        $code = 400;
        return response()->json([
            'status' => 'error',
            'message' => 'client error',
            'data' => $message,
        ], $code);
    }

    protected function showResponseServerError($message)
    {
        $code = 500;
        return response()->json([
            'status' => 'error',
            'message' => 'server error',
            'data' => $message,
        ], $code);
    }

    protected function showResponseMaintenance()
    {
        $code = 500;
        return response()->json([
            'status' => 'error',
            'message' => 'maintenance',
            'data' => 'this endpoint is being fixed !',
        ], $code);
    }

    protected function showResponseFailedMutation($msg)
    {
        $code = 500;
        $message = "failed to mutate data, rollback transaction (err : $msg)";
        return response()->json([
            'status' => 'error',
            'message' => 'failed mutate data',
            'data' => $message,
        ], $code);
    }

    protected function showResponsePaginate($myQuery,  $columnSorts = [], $columnFilters = [], $columnFiltersExact = [])
    {
        $resultQueryBuilder = $this->setQueryBuilder($myQuery, $columnSorts, $columnFilters, $columnFiltersExact);
        $res = [
            'status' => 'success',
            'message' => 'success get data',
            'data' => $resultQueryBuilder->items(),
            'pagination' => [
                'current_page' => (int) $resultQueryBuilder->currentPage(),
                'total' => (int) $resultQueryBuilder->total(),
                'per_page' => (int) $resultQueryBuilder->perPage(),
                'last_page' => (int) $resultQueryBuilder->lastPage(),
            ],
        ];
        return $res;
    }

    protected function showReponseDownload($fileName)
    {
        $timestamp = Carbon::now()->timestamp;
        $isExternalResource = false;
        $file = null;
        if ($isExternalResource) {
            // External
            $ext = explode('.', $fileName);
            $ext = end($ext);
            $nameDownload = "$timestamp.$ext";
            $url = config('const.webUrl');
            $path = "$url/$fileName";
            $mime = MimeType::detectByFileExtension($fileName);
            $destinationPath = public_path() . "/temporaries/$nameDownload";
            copy($path, $destinationPath);
            $headers = array(
                'Content-Type: ' . $mime,
            );
            return response()->download($destinationPath, $nameDownload, $headers)->deleteFileAfterSend();
        } else {
            $folderPath = public_path() . "/files/";
            $file = $folderPath . $fileName;
            $headers = array(
                'Content-Type: ' . mime_content_type($file),
            );
            if (!$file) return $this->showResponseError('data not found');
            return response()->download($file, $fileName, $headers);
        }
    }
}
