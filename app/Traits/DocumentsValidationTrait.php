<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

/**
 * Trait DocumentValidationTrait
 *
 * Provides helper methods for validating CPF, CNPJ, and email formats.
 */
trait DocumentsValidationTrait
{
    /**
     * Validate if a CPF is in a valid format.
     *
     * @param  string  $cpf  The CPF number to validate.
     *
     * @return bool True if valid, false otherwise.
     */
    protected function isCPFValid(string $cpf): bool
    {
        $validator = Validator::make(
            ['cpf' => $cpf],
            ['cpf' => 'cpf']
        );

        return $validator->passes();
    }

    /**
     * Validate if a CNPJ is in a valid format.
     *
     * @param  string  $cnpj  The CNPJ number to validate.
     *
     * @return bool True if valid, false otherwise.
     */
    protected function isCNPJValid(string $cnpj): bool
    {
        $validator = Validator::make(
            ['cnpj' => $cnpj],
            ['cnpj' => 'cnpj']
        );

        return $validator->passes();
    }
}
