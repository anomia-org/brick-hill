<?php

namespace App\Constants\Thumbnails;

enum ThumbnailSize: int
{
    case LARGE = 512;
    case ITEM_LARGE = 375;
    case MEDIUM = 256;
    case SMALL = 128;
}
