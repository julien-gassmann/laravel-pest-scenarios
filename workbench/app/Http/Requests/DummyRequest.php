<?php

namespace Workbench\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Workbench\App\Models\User;

class DummyRequest extends FormRequest
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
     * @return array<string, (Rule|string)[]>
     */
    public function rules(): array
    {
        $isUpdateRoute = $this->routeIs('*dummies.update');

        return [
            'name' => [$isUpdateRoute ? 'sometimes' : '', 'required', 'string', 'between:3,50'],
            'email' => [$isUpdateRoute ? 'sometimes' : '', 'required', 'email', Rule::unique('dummies')->ignore($this->route('dummy'))],
            'age' => ['nullable', 'integer', 'min:18'],
            'is_active' => ['nullable', 'boolean'],
            'children_ids' => ['nullable', 'array'],
            'children_ids.*' => ['integer', 'exists:dummy_children,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'children_ids' => 'children ids',
        ];
    }
}
