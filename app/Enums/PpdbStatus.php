<?php

namespace App\Enums;

enum PpdbStatus: string
{
    case SUBMITTED = 'submitted';
    case APPROVED  = 'approved';
    case REJECTED  = 'rejected';
    case ACTIVATED = 'activated';
}
