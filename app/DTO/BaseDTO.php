<?php

namespace App\DTO;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Class BaseDTO
 *
 * Abstract class BaseDTO providing methods for array and JSON conversion.
 *
 * This class provides methods to convert DTO objects to arrays and JSON,
 * both with and without null values.
 */
abstract class BaseDTO
{
    /**
     * Convert DTO to an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return (array) $this;
    }

    /**
     * Convert DTO to JSON
     *
     * @return string
     */
    public function toJson(): string
    {
        $array = get_object_vars($this);

        return json_encode($array);
    }

    /**
     * Convert DTO to array without null values
     *
     * @return array
     */
    public function notNullToArray(): array
    {
        return json_decode($this->notNullToJson(), true);
    }

    /**
     * Convert DTO to JSON without null values
     *
     * @return string
     */
    public function notNullToJson(): string
    {
        $data = get_object_vars($this);

        $array = array_filter($data, function ($value) {
            return $value !== null;
        });

        return json_encode($array);
    }

    /**
     * Assign values from request parameters to DTO variables.
     *
     * @param  FormRequest  $request
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function assignFromRequest(FormRequest $request): self
    {
        foreach ($request->validated() as $key => $value) {
            $camelCaseKey = Str::camel($key);
            if (!property_exists($this, $camelCaseKey)) {
                throw new InvalidArgumentException("The key '{$key}' does not exist in the DTO.");
            }

            $this->$camelCaseKey = $value;
        }

        return $this;
    }

    /**
     * Assign values from repository response to DTO variables.
     *
     * @param  Collection  $request
     *
     * @return self
     *
     * @throws InvalidArgumentException
     */
    public function assignFromRepository(Collection $request): self
    {
        foreach ($request as $key => $value) {
            $camelCaseKey = Str::camel($key);
            if (!property_exists($this, $camelCaseKey)) {
                throw new InvalidArgumentException("The key '{$key}' does not exist in the DTO.");
            }

            $this->$camelCaseKey = $value;
        }

        return $this;
    }
}
