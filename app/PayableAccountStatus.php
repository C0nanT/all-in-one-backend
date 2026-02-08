<?php

namespace App;

enum PayableAccountStatus: string
{
    case Open = 'open';
    case Paid = 'paid';
}
