<?php

namespace App\Setting\Entity;

enum BannerType: string
{
    case MAIN = 'main';
    case EXTRA = 'extra';
    case ALL = 'all';
    case MOBILE = 'mobile';
}
