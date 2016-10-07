<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{

    const START_USING = 0, // show /add button
        ADD_AWAITING = 1; // wait for add vk user

    protected $table = 'telegram_users';

    protected $fillable = ['username', 'first_name', 'last_name'];


}
