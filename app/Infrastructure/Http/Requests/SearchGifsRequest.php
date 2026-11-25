<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchGifsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string'],
            'limit' => ['sometimes', 'integer', 'min:1'],
            'offset' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
