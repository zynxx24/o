<?php
namespace App\Concerns;
use App\Models\User;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    protected function profileRules(?int $userId = null): array
    {
        return [
            'name' => $this->nameRules(),
            'username' => $this->usernameRules($userId),
            'email' => $this->emailRules($userId),
        ];
    }

    protected function nameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    protected function usernameRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'max:255',
            'alpha_dash',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }

    protected function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}
