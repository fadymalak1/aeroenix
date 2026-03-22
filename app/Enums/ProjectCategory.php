<?php

namespace App\Enums;

enum ProjectCategory: string
{
    case WebApplication = 'Web Application';
    case MobileApp = 'Mobile App';
    case CloudSolution = 'Cloud Solution';
    case AiData = 'AI & Data';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
