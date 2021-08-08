<?php

namespace App\Interfaces;

use MyCLabs\Enum\Enum;

/**
 * @method static RolesEnum SIMPLE()
 * @method static RolesEnum ADMIN()
 */

final class RolesEnum extends Enum
{
    private const SIMPLE = 'simple';
    private const ADMIN = 'admin';
}
