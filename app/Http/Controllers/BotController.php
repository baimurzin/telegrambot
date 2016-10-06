<?php

namespace App\Http\Controllers;

use App\TelegramUser;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class BotController extends Controller
{

    /**
     * BotController constructor.
     */
    public function __construct()
    {
    }

    public function handle(Request $request)
    {
        $input = $request->input('message');
        if (!$input)
            return;

        $user = $this->getUserFromRequest($request);

    }

    /**
     * @param Request $request
     * @return User
     */
    private function getUserFromRequest(Request $request)
    {
        if (!Input::has('message'))
            return null;
        $message = $request->input('message');
        if (!isset($message['from']) && !$message['from'])
            return null;
        $user_data = $message['from'];
        $user = TelegramUser::where('t_id', $user_data['from'])->first();
        if ($user)
            return $user;
        $user = new TelegramUser();
        $user->t_id = $user_data['id'];
        $user->first_name = $user_data['first_name'];
        $user->last_name = $user_data['last_name'];
        $user->username = $user_data['username'];
        $user->save();
        return $user;
    }
}
