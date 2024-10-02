<?php
/**
 * User: LAHAN Tresor
 * Date: 2/10/2024
 * Time: 10:21 AM
 */

namespace App\Http\Enums;

enum GroupUserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';
}
