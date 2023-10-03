<?php

namespace App\Enums;

enum StatusEnum: int
{
    case Open = 1;

    case Considering = 3;

    case InProgress = 4;

    case Closes = 5;
}
