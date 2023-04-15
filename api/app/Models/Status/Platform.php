<?php

namespace App\Models\Status;

enum Platform: string
{
    case WEB = 'web';
    case IOS = 'ios';
    case ANDROID = 'android';
}
