<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadBillRequest extends FormRequest
{
    public function rules()
    {
        return [
            'bill_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }
}
