<?php

namespace Jgss\LaravelPestScenarios\Definitions\Scenarios\Traits;

use Illuminate\Support\Facades\Lang;

trait BuildsValidationMessages
{
    /**
     * @return array<string, string[]>
     */
    public function buildExpectedMessages(): array
    {
        $attributes = $this->context->getFormRequestInstance()->attributes();
        $expectedMessages = [];

        foreach ($this->expectedValidationErrors as $field => $expectedValidationErrors) {
            $attributeLabel = array_key_exists($field, $attributes) && is_string($attributes[$field])
                ? $attributes[$field]
                : $field;
            $expectedMessages[$field] = array_map(
                fn ($message) => $this->getValidationErrorMessage($message, $attributeLabel),
                $expectedValidationErrors
            );
        }

        return $expectedMessages;
    }

    /**
     * Builds a localized validation error message by dynamically injecting replacement values.
     *
     * Accepts either:
     *   - A rule descriptor string (e.g., 'between.numeric|min=1|max=100')
     *   - A raw message string (e.g., 'This field is required.')
     *
     * If the descriptor matches a Laravel validation key, it injects attribute and parameters;
     * otherwise, the message is returned as-is.
     *
     * Example input: message 'between.numeric|min=1|max=100' with attribute 'age'
     * Example output: "The age must be between 1 and 100."
     */
    private function getValidationErrorMessage(string $message, string $attribute): string
    {
        // Use the current app locale
        $locale = app()->getLocale();

        // Split the rule string into rule name and replacements (ex: "between|min=1|max=100")
        // + [1 => ''] ensures $rawReplacements is defined even if no extra replacements are provided
        [$messagePath, $rawReplacements] = explode('|', $message, 2) + [1 => ''];

        // If path is not doesn't exist, return raw error message
        if (! Lang::hasForLocale('validation.'.$messagePath, $locale)) {
            return $message;
        }

        // Check if a custom translation exists for the attribute, otherwise fallback to the raw attribute name
        $attributeKey = "validation.attributes.$attribute";
        $attribute = Lang::hasForLocale($attributeKey, $locale)
            ? __($attributeKey, [], $locale)
            : $attribute;

        // Initialize replacements with the attribute name
        $replacements = ['attribute' => $attribute];

        // Parse additional replacement placeholders (like min, max) from the rule string
        foreach (explode('|', $rawReplacements) as $pair) {
            if (str_contains($pair, '=')) {
                // Split each placeholder into key and value
                [$key, $value] = explode('=', $pair, 2);
                $replacements[$key] = $value;
            }
        }

        // Translate the validation message using the rule and replacements
        /** @var string $validationErrorMessage */
        $validationErrorMessage = __('validation.'.$messagePath, $replacements, $locale);

        return $validationErrorMessage;
    }
}
