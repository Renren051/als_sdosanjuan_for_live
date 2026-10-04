<?php

namespace App\Http\Requests;

use App\Models\ModuleProgress;
use Illuminate\Foundation\Http\FormRequest;

class SyncProgressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_identifier' => ['nullable', 'string', 'max:150'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.module_id' => ['required', 'exists:modules,id'],
            'items.*.downloaded_at' => ['nullable', 'date'],
            'items.*.last_opened_at' => ['nullable', 'date'],
            'items.*.completion_status' => ['required', 'in:' . implode(',', [
                ModuleProgress::STATUS_DOWNLOADED,
                ModuleProgress::STATUS_IN_PROGRESS,
                ModuleProgress::STATUS_COMPLETED,
            ])],
            'items.*.last_page_read' => ['nullable', 'integer', 'min:1'],
            'items.*.total_pages' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
