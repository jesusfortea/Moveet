<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SpanishIdentityCard implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dni = strtoupper(str_replace([' ', '-'], '', $value));

        if (!preg_match('/^[0-9XYZ][0-9]{7}[A-Z]$/', $dni)) {
            $fail('El formato del DNI/NIE no es válido (ej: 12345678A).');
            return;
        }

        $numberPart = substr($dni, 0, 8);
        $letter = substr($dni, -1);

        // NIE integration
        $numberPart = str_replace(['X', 'Y', 'Z'], [0, 1, 2], $numberPart);

        $validLetters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $calculatedLetter = $validLetters[(int) $numberPart % 23];

        if ($letter !== $calculatedLetter) {
            $fail('La letra del DNI/NIE no se corresponde con el número proporcionado.');
        }
    }
}
