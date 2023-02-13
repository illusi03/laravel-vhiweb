<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function photo(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|file|image|max:10240',
            'description' => 'required',
        ]);
        // menyimpan data file yang diupload ke variabel $file
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $fileName = 'post-' . time() . '.' . $extension;
        // Melalui Storage lalu di simlink
        // How to simlink, php artisan simlink
        // $tmp = $file->storeAs('images', $fileName);

        // Melalui tmp -> langsung di move ke public
        $folder = 'images';
        $file->move($folder, $fileName);
        $actualLocate = "/$folder/$fileName";
        return $this->getResponse([
            'message' => 'File was uploaded',
            'location' => url($actualLocate),
        ]);
    }
}
