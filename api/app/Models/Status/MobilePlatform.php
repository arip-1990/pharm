<?php

namespace App\Models\Status;

enum MobilePlatform: string
{
    case WEB = 'web';
    case IOS = 'ios';
    case ANDROID = 'android';
}
