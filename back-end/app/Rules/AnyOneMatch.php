<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AnyOneMatch implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($conditions)
    {
        $this->match_conditions = $conditions;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($this->match_conditions as $condition) {
            if($value == $condition) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '条件に合いませんでした';
    }
}
