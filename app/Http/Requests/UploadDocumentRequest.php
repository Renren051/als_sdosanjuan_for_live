<?php

namespace App\Http\Requests;

use App\Models\LearnerDocument;
use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_type' => ['required', 'in:' . implode(',', [
                LearnerDocument::TYPE_PSA_BIRTH_CERT,
                LearnerDocument::TYPE_BARANGAY_CLEARANCE,
                LearnerDocument::TYPE_FORM_137_SF10,
                LearnerDocument::TYPE_PHOTO_ID,
                LearnerDocument::TYPE_PORTFOLIO_DOC,
            ])],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'], // 10MB limit
            'enrollment_id' => ['nullable', 'exists:enrollments,id'],
        ];
    }
}
