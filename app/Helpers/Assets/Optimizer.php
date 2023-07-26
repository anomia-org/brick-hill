<?php

namespace App\Helpers\Assets;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;

use App\Exceptions\Custom\APIException;

class Optimizer
{
    public int $widthConstraint = 512;
    public int $heightConstraint = 512;

    /**
     * Takes uploaded file and optimizes it
     *
     * @param UploadedFile $file
     * @return string
     */
    public function optimize(UploadedFile $file): string
    {
        return match ($file->guessExtension()) {
            // is there any optimization to be done to a txt?
            // this will be matched for objs
            'txt', 'gif' => $file->get(),
            'png', 'jpg', 'jpeg' => $this->pngquant($file),
            default => throw new \App\Exceptions\Custom\InvalidDataException('Invalid file attempting to be uploaded')
        };
    }

    /**
     * Constrain an optimized image to a particular size
     * 
     * @param int $width 
     * @param int $height 
     * @return void 
     */
    public function setConstraint(int $width, int $height)
    {
        $this->widthConstraint = $width;
        $this->heightConstraint = $height;
    }

    /**
     * Compresses UploadedFile through pngquant
     * 
     * Should be moved to a lambda that then saves the final file
     * 
     * @param UploadedFile|string $file
     * @return string
     */
    public function pngquant(UploadedFile|string $file): string
    {
        $img = Image::make($file);

        $img = $img->fit($this->widthConstraint, $this->heightConstraint, function ($constraint) {
            $constraint->upsize();
        });

        $img = $img->encode('png')->stream();

        $descriptorspec = [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']];
        $proc = proc_open('/usr/bin/pngquant - --speed 1 --skip-if-larger', $descriptorspec, $pipes);
        if (!is_resource($proc)) {
            Log::error("Optimizer Proc is not a resource?");
            throw new APIException("Unexpected error in compressing image");
        }

        fwrite($pipes[0], $img);
        fclose($pipes[0]);

        $compressed = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        proc_close($proc);

        if (!$compressed || $errors) {
            Log::error($errors);

            throw new APIException('Error in compressing image');
        }

        return $compressed;
    }
}
