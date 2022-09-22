<?php

namespace App\Rules;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Contracts\Validation\InvokableRule;

class CustomDate implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        try {
            $date = \Carbon\Carbon::createFromFormat('D M d Y H:i:s e+', $value);
        }
        catch (InvalidFormatException $e) {
            $fail('Недопустимый формат времени!');
        }
    }
}
