<?php

namespace App\Http\Requests;

use App\Exceptions\InvalidParamException;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

/**
 * Class BaseRequest
 *
 * An abstract base class for custom form requests.
 * Provides common validation behavior, including custom error handling,
 * localization setup, and value conversion helpers.
 */
abstract class BaseRequest extends FormRequest
{
    protected string $message = 'validation errors';
    protected string $userMessage = 'erro nos dados enviados';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always true by default.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define validation rules for the request.
     *
     * @return array<string, mixed>
     */
    abstract public function rules(): array;

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator  The validator instance with error details.
     *
     * @return void
     *
     * @throws InvalidParamException Always thrown with custom structured error.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new InvalidParamException(
            'V001',
            $this->message,
            $this->userMessage,
            $validator->errors()->jsonSerialize()
        );
    }

    /**
     * Prepare the data for validation.
     * Sets the application locale (e.g., based on request headers).
     */
    protected function prepareForValidation(): void
    {
        App::setLocale(config('app.locale'));
    }

    /**
     * Convert a mixed value to a boolean.
     *
     * @param  mixed  $value  The value to convert.
     *
     * @return bool|null True, false, or null if unrecognized.
     */
    protected function convertToBoolean(mixed $value): ?bool
    {
        $normalized = strtolower((string) $value);

        return match ($normalized) {
            'true' => true,
            'false' => false,
            default => null,
        };
    }

    /**
     * Convert a mixed value to an integer.
     *
     * @param  mixed  $value  The value to convert.
     * @param  int  $default  The default value if conversion fails.
     *
     * @return int
     */
    protected function convertToInteger(mixed $value, int $default = 0): int
    {
        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * Merges a converted boolean value into the request.
     *
     * Converts the input value to boolean using convertToBoolean().
     * If the property is not present or conversion returns null,
     * uses the provided default value.
     *
     * @param  string  $property  The request property to process.
     * @param  bool  $default  The default value to use if the parameter is not present. Default is false.
     *
     * @return void
     */
    protected function mergeBoolean(string $property, bool $default = false): void
    {
        if ($this->has($property)) {
            $converted = $this->convertToBoolean($this->input($property));
            if (!is_null($converted)) {
                $this->merge([$property => $converted]);

                return;
            }
        }

        $this->merge([$property => $default]);
    }

    /**
     * Merge an integer-converted input into the request.
     *
     * @param  string  $property  The input key to check and convert.
     * @param  int  $default  The default value if conversion fails.
     *
     * @return void
     */
    protected function mergeInteger(string $property, int $default = 0): void
    {
        $this->merge([$property => $this->convertToInteger($this->input($property, $default), $default)]);
    }

    /**
     * Merge a date string into the request as a Carbon instance.
     *
     * Parses the given date string into a Carbon object and merges it into
     * the current request payload under the specified property name.
     *
     * @param  string  $property  The request field name to be merged.
     * @param  string  $date  The date string to be parsed (any Carbon-compatible format).
     *
     * @return void
     *
     * @throws InvalidFormatException When the date string cannot be parsed.
     */
    protected function mergeDate(string $property, string $date): void
    {
        try {
            $this->merge([$property => Carbon::parse($date)]);
        } catch (InvalidFormatException) {
            throw new InvalidParamException('C001', 'invalid date parameter', 'parâmetro de data inválido');
        }
    }
}
