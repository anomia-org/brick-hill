<?php

namespace App\Constants;

enum ValueDirection: int
{
    case INCREASING = 1;
    case NEUTRAL = 0;
    case DECREASING = -1;
}