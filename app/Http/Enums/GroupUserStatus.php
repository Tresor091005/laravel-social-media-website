<?php
/**
 * User: LAHAN Tresor
 * Date: 2/10/2024
 * Time: 10:22 AM
 */

namespace App\Http\Enums;

enum GroupUserStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
