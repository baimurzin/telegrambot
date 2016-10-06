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
        $message = $request->input('message');
        Storage::put('test.file', json_encode($message));


        $user = $this->getUserFromRequest($message);

        if ($message['text'])
            $handle_result = $this->textHandle($message['text'], $user);

    }

    /**
     * @param $message array
     * @return User
     */
    private function getUserFromRequest($message)
    {
        if (!$message['from'])
            return null;
        $user_data = $message['from'];
        $user = TelegramUser::where('t_id', $user_data['id'])->first();
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


    /**
     * @param $message array
     * @return bool
     */
    private function textHandle($message, $user)
    {
        if (!$message)
            return false;

        switch ($message['text']) {
            case '/add':
                Telegram::sendMessage([
                    'chat_id' => $user->t_id,
                    'text' => 'Укажите профиль пользователя: '
                ]);
                break;

            default:
                Telegram::sendMessage([
                    'chat_id' => $user->t_id,
                    'text' => $user->first_name . ', привет!'
                ]);
                break;
        }

    }
}
