<?php

namespace App\Constants\Thumbnails;

enum ThumbnailState: string
{
    case APPROVED = 'approved';
    case PENDING = 'pending';
    case DECLINED = 'declined';
    case AWAITING_APPROVAL = 'awaiting_approval';
}
