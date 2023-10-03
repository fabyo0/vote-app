<?php

namespace App\Enums;

enum StatusEnum: int
{
    case Open = 1;

    case Considering = 2;

    case InProgress = 3;

    case Implemented = 4;

    case Closes = 5;
}
