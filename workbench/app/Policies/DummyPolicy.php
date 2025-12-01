<?php

/** @noinspection PhpUnused */

namespace Workbench\App\Policies;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable as User;

class DummyPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function methodWithoutParameter(?User $authUser): bool
    {
        return ! is_null($authUser);
    }

    public function methodWithParameter(?User $authUser, bool $parameter): bool
    {
        return ! is_null($authUser) && $parameter;
    }

    /**
     * @throws Exception
     */
    public function methodThrowingException(): void
    {
        throw new Exception('dummy exception message');
    }
}
