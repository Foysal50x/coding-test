<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{

    public function __invoke(Request $request)
    {
        $filePath = Storage::put("public/images", $request->file('file'));
        return Response::json(["success" => true, "images" => $filePath]);
    }
}
