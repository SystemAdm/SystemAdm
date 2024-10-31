<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request,$type) {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048' // example for images or pdf files
        ]);
        if (request()->hasFile('file')) {
            $fileName = time() . '_' . $request->file->getClientOriginalName();
            $filePath = $request->file('file')->storeAs('images'.DIRECTORY_SEPARATOR.$type, $fileName, 'public');
            return response()->json(['file'=>$filePath]);
        }
        return response()->json(['message' => 'No file was uploaded.'], 400);
    }
}
