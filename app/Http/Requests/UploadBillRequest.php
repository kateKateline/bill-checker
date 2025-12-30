<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadBillRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bill_file' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120', // 5MB
            ],
        ];
    }
}
