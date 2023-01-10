<?php

namespace Ynotz\EasyAdmin\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Ynotz\SmartPages\Http\Controllers\SmartController;
use Ynotz\EasyAdmin\Services\DashboardServiceInterface;
use Ynotz\EasyAdmin\Services\ImageService;

class MasterController extends SmartController
{
    public function fetch($service, $method)
    {
        try {
            return response()->json([
                'success' => true,
                'results' => (app()->make($service))->$method($this->request->input('value'))
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->__toString()
            ]);
        }
    }

    public function filepondUpload()
    {
        $file = $this->request->file('file');

        $name = $file->getClientOriginalName();
        $name = str_replace($file->getClientOriginalExtension(), '', $name);
        $name = Str::swap([' ' => '', '.' =>'', '_' => '', '-' => ''], $name);
        $name = time().rand(0,99).'_'.substr($name, 0, 20).'.'.$file->extension();

        $tempFolder = config('mediaManager.temp_folder');
        $tempDisk = config('mediaManager.temp_disk');
        trim($file->storeAs($tempFolder.'/', $name, $tempDisk));

        return response()->json([
            'path' => $name
        ]);
    }

    public function filepondDelete()
    {
        info($this->request->input('file'));
        Storage::delete(trim($this->request->input('file')));
        return response()->json([
            'success' => true
        ]);
    }

    public function displayImage($variant, $ulid, $imagename)
    {
        $path = storage_path('images/' . $ulid.'/'.$variant.'/'.$imagename);

        if (!File::exists($path)) {
            ImageService::makeVariant($variant, $ulid);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
}
