<?php

namespace App\Enums;

enum LinkTarget: string
{
    case BLANK = '_blank';
    case SELF = '_self';
    case PARENT = '_parent';
    case TOP = '_top';
}