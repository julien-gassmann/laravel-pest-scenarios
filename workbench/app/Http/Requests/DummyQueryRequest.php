<?php

namespace Workbench\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Workbench\App\Models\User;

class DummyQueryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var ?User $user */
        $user = Auth::user();

        return $user && $user->name !== 'Unauthorized';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string[]>
     */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'gte:1'],
            'perPage' => ['nullable', 'integer', 'between:1,10'],
            'sort' => ['nullable', 'string', 'in:name,email'],
        ];
    }
}
