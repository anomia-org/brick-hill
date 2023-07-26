<?php

namespace App\Http\Requests\User\Report;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\User\Message;

use App\Rules\Validation\PolyType;

class SendReport extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(request()->reportable_type == 'message') {
            $message = Message::findOrFail(request()->reportable_id);
            return $message->author_id == auth()->id() || $message->recipient_id == auth()->id();
        }
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
            'reportable_id' => ['required', new PolyType('reportable_type')],
            'reason' => 'required|exists:report_reasons,id',
            'note' => 'max:250'
        ];
    }
}
