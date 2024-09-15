<?php
/**
 * User: LAHAN Tresor
 * Date: 15/9/2024
 * Time: 8:09 PM
 */

namespace App\Http\Enums;

enum PostReactionEnum: string
{
    case LIKE = 'like';
    case LOVE = 'love';
    case SAD = 'sad';
}
