<?php

namespace App\Enums;

enum PageStatus: string
{
    case Published = 'published';
    case Draft = 'draft';
}
