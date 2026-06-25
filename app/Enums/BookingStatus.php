<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Locked = 'locked';
    case PendingPayment = 'pending_payment';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
}
