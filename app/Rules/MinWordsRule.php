<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MinWordsRule implements Rule
{
    /**
     * The minimum number of words.
     *
     * @var int
     */
    protected $minWords;

    public function __construct($minWords)
    {
        $this->minWords = $minWords;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return count(explode(' ', $value)) >= $this->minWords;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute must have at least 2 words.';
    }
}
