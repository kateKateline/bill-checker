<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Upload Bill Request
 * 
 * Validates bill file upload requests.
 */
class UploadBillRequest extends FormRequest
{
    /**
     * Get validation rules
     * 
     * @return array
     */
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
