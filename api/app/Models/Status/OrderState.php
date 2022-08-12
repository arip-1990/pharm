<?php

namespace App\Models\Status;

enum OrderState: int
{
    case STATE_WAIT = 0;
    case STATE_ERROR = 1;
    case STATE_SUCCESS = 2;
}
