<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UniqueEmailInCompany implements Rule
{
    private $companyId;

    private $skipId;

    /**
     * UniqueEmailInCompany constructor.
     * @param $companyId
     */
    public function __construct($companyId, $skipId = null)
    {
        $this->companyId = $companyId;
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
        $user = $user->first();
        if ($user) {
            $userExistsInCompany = $user->companies()->where('companies.id', $this->companyId)->count() > 0;
            return !$userExistsInCompany;
        }
        return true;
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
