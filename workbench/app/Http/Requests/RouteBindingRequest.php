<?php

namespace Workbench\App\Http\Requests;

use BackedEnum;
use Illuminate\Foundation\Http\FormRequest;
use Workbench\App\Models\Dummy;
use Workbench\App\Models\DummyChild;
use Workbench\App\Services\DummyService;

class RouteBindingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $routeName = $this->route()->getName();

        return match ($routeName) {
            'api.multiple.bindings' => $this->checkMultipleBindings(),
            'api.model.column.binding' => $this->checkModelColumnBinding(),
            'api.built.in.binding' => $this->checkBuiltInBinding(),
            'api.class.binding' => $this->checkClassInstanceBinding(),
            'api.enum.binding' => $this->checkEnumBinding(),
            default => false,
        };
    }

    public function checkMultipleBindings(): bool
    {
        return $this->route('dummy') instanceof Dummy
            && $this->route('dummyChild') instanceof DummyChild;
    }

    public function checkModelColumnBinding(): bool
    {
        return $this->route('dummy') instanceof Dummy;
    }

    public function checkBuiltInBinding(): bool
    {
        return is_scalar($this->route('int'));
    }

    public function checkClassInstanceBinding(): bool
    {
        $class = $this->route('class');

        return $class instanceof DummyService
            && $class->property === 'bind dummy service';
    }

    public function checkEnumBinding(): bool
    {
        return $this->route('enum') instanceof BackedEnum;
    }
}
