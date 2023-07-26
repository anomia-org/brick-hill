<?php

namespace App\Http\Requests\General;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

use App\Constants\Thumbnails\ThumbnailSize;
use App\Constants\Thumbnails\ThumbnailType;

class BulkThumbnail extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'thumbnails' => 'required|array|max:100',
            'thumbnails.*' => 'required|distinct',
            // TODO: implement sizing api to cut down on bandwidth
            //'thumbnails.*.size' => ['required', new Enum(ThumbnailSize::class)],
            'thumbnails.*.type' => ['required', new Enum(ThumbnailType::class)]
        ];
    }
}
