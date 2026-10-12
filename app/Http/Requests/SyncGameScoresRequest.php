<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncGameScoresRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'device_identifier' => ['nullable', 'string', 'max:150'],
            'attempts' => ['required', 'array', 'min:1', 'max:50'],
            'attempts.*.attempt_uuid' => ['required', 'uuid'],
            'attempts.*.module_id' => ['required', 'integer', 'exists:modules,id'],
            'attempts.*.game_type' => ['required', 'in:memory_match,blp_word_match,elementary_number_trail,secondary_brain_challenge,blp_literacy_quiz,elementary_math_quiz,secondary_reasoning_quiz'],
            'attempts.*.score' => ['required', 'integer', 'between:0,100'],
            'attempts.*.max_score' => ['required', 'integer', 'in:100'],
            'attempts.*.moves' => ['required', 'integer', 'between:3,65535'],
            'attempts.*.elapsed_seconds' => ['required', 'integer', 'between:0,86400'],
            'attempts.*.attempted_at' => ['required', 'date'],
        ];
    }
}
