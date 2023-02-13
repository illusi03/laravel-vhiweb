<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait UploadTrait
{
    protected function fileUploadMultiple(
        $uploadedFiles,
        $filename = null,
        $folder = null,
        $disk = "public"
    ) {
        if ($folder) {
            $folder = "/" . "uploads" . "/" . $folder . "/";
        } else {
            $folder = "/" . "uploads" . "/";
        }
        $resultPaths = [];
        if (!$uploadedFiles) return null;
        if (!is_array($uploadedFiles)) $uploadedFiles = [$uploadedFiles];
        $counter = 1;
        array_map(function ($item) use ($disk, $filename, $folder, &$resultPaths, &$counter) {
            $typeUploadFile = gettype($item);
            $isUploadFileType = $typeUploadFile == 'object';
            if ($item && $isUploadFileType) {
                $extension = File::extension($item->getClientOriginalName());
                $yearNumber = date('Y');
                $monthNumber = date('M');
                $time = time();
                $name = !is_null($filename) ? $filename : str_random(25);
                $name = "$yearNumber-$monthNumber-$time-$name-$counter";
                $item->storeAs($folder, $name . '.' . $item->getClientOriginalExtension(), $disk);
                $filePath = $folder . $name . '.' . $extension;
                array_push($resultPaths, $filePath);
                $counter++;
            }
        }, $uploadedFiles);
        return $resultPaths;
    }

    protected function fileUploadOne(
        $uploadedFile,
        $filename = null,
        $folder = null,
        $disk = "public"
    ) {
        if ($folder) {
            $folder = "/" . "uploads" . "/" . $folder . "/";
        } else {
            $folder = "/" . "uploads" . "/";
        }
        // Get Extension Version
        // $extension = $uploadedFile->getClientOriginalExtension();
        $typeUploadFile = gettype($uploadedFile);
        $isUploadFileType = $typeUploadFile == 'object';
        if ($uploadedFile && $isUploadFileType) {
            $extension = File::extension($uploadedFile->getClientOriginalName());
            $yearNumber = date('Y');
            $monthNumber = date('M');
            $time = time();
            $name = !is_null($filename) ? $filename : str_random(25);
            $name = "$yearNumber-$monthNumber-$time-$name";
            $uploadedFile->storeAs($folder, $name . '.' . $uploadedFile->getClientOriginalExtension(), $disk);
            $filePath = $folder . $name . '.' . $extension;
            return $filePath;
        }
        return null;
    }

    public function fileDeleteOne($fullPath = null, $disk = "public")
    {
        return Storage::disk($disk)->delete($fullPath);
    }
}
