<?php
namespace App\Helper;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\File;

class ImageHelper
{
    public static function UploadImage($file)
    {
        $imageManager = new ImageManager(Driver::class);

        // Thư mục lưu ảnh trong storage/app/public/image
        $destinationPath = storage_path('app/public/image/');

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $imageHandle = $imageManager->read($file);
        $imageHandle->resize(1920, 1920, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save($destinationPath . $fileName);

        return $fileName;
    }

    public static function GetImageUrl($fileName)
    {
        return asset("storage/image/" . $fileName);
    }

    public static function DeleteImage($fileName)
    {
        $imagePath = storage_path('app/public/image/') . $fileName;

        if (file_exists($imagePath)) {
            unlink($imagePath);
        } else {
            throw new \Exception("Image not found: $fileName");
        }
    }
}
