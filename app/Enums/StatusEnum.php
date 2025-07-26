<?php

namespace App\Enums;

enum StatusEnum: int
{
    case Open = 1;

    case Considering = 2;

    case InProgress = 3;

    case Implemented = 4;

    case Closes = 5;

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Considering => 'Considering',
            self::InProgress => 'In Progress',
            self::Implemented => 'Implemented',
            self::Closes => 'Closed',
        };
    }
}
