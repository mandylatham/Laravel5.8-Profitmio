<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UniqueEmailInDatabase implements Rule
{
    private $skipId;

    /**
     * UniqueEmailInCompany constructor.
     * @param $skipId
     */
    public function __construct($skipId = null)
    {
        $this->skipId = $skipId;
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
        $user = User::where('email', $value);
        if ($this->skipId) {
            $user->where('id', '<>', $this->skipId)->first();
        }
        return $user->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
