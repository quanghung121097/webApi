<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class ImageService
{
    const IMG_TYPES = ["jpeg","png","jpg"];
    public function saveImgBase64($param, $folder = 'uploads/products')
    {
        list($extension, $content) = explode(';', $param);
        $tmpExtension = explode('/', $extension);
        preg_match('/.([0-9]+) /', microtime(), $m);
        $fileName = sprintf('img%s%s.%s', date('YmdHis'), $m[1], $tmpExtension[1]);
        
        $content = explode(',', $content)[1];
        $storage = Storage::disk('public');

        $checkDirectory = $storage->exists($folder);

        if (!$checkDirectory) {
            $storage->makeDirectory($folder);
        }

        $storage->put($folder . '/' . $fileName, base64_decode($content), 'public');

        return $fileName;
    }

    public function checkTypeAndSizeImage($images){
        foreach($images as $key => $image){
            list($extension, $content) = explode(';', $image);
            $tmpExtension = explode('/', $extension);
            if(!in_array($tmpExtension[1], self::IMG_TYPES)){
                return response(['success' => false, 'message'=> 'Ảnh '.$key.' không đúng định dạng cho phép'],400);
            }
        }
    }
}
