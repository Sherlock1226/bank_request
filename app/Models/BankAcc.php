<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class BankAcc extends Model
{

    use Notifiable;

    /**
     * @var mixed
     */
    protected $table = 'bankacc';


}
