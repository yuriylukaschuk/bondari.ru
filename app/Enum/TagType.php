<?php

namespace App\Enum;

enum TagType : int
{
    case MainTitle = 1;
    case SubTitle = 2;
    case LargeText = 3;
    case SmallText = 4;
    case NumberedList = 5;
    case BulletedList = 6;
    case Photo = 7;
    case PhotoSet = 8;
}
