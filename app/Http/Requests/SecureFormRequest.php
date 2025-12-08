<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SecureFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Default to false, to be overridden in child classes
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Sanitize input data before validation
     */
    protected function prepareForValidation()
    {
        $input = $this->all();

        // Sanitize all input data to prevent XSS
        $this->sanitizeInput($input);

        $this->replace($input);
    }

    /**
     * Recursively sanitize input data to prevent XSS
     */
    private function sanitizeInput(array &$input)
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $this->sanitizeInput($input[$key]);
            } elseif (is_string($value)) {
                // Sanitize string values
                $input[$key] = strip_tags($value);

                // Additional XSS prevention for rich text
                if (str_contains($key, 'description') || str_contains($key, 'content') || str_contains($key, 'message')) {
                    $input[$key] = $this->sanitizeRichText($value);
                }
            }
        }
    }

    /**
     * Sanitize rich text content
     */
    private function sanitizeRichText($content)
    {
        // Allow only safe HTML tags for rich text
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6>';

        // Remove any potentially dangerous tags/attributes
        $content = strip_tags($content, $allowedTags);

        return $content;
    }
}
