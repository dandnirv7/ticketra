<?php

namespace App\Enums;

enum SeatStatus: string
{
    case Tersedia = 'tersedia';
    case Dikunci = 'dikunci';
    case Terjual = 'terjual';
    case Dilepas = 'dilepas';
}
