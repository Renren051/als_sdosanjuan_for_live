<?php

namespace App\Http\Requests;

use App\Models\Module;
use Illuminate\Foundation\Http\FormRequest;

class ModuleUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'strand_id' => ['required', 'exists:learning_strands,id'],
            'level' => ['required', 'in:' . implode(',', [
                Module::LEVEL_BLP,
                Module::LEVEL_ELEMENTARY,
                Module::LEVEL_JUNIOR_HIGH,
            ])],
            'module_number' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'pdf_file' => [$isUpdate ? 'nullable' : 'required', 'file', 'mimes:pdf', 'max:25600'], // 25MB max
            'version' => ['nullable', 'string', 'max:20'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
