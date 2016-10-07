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

        $user = $this->getUserFromRequest($message);

        $this->handleSteps($user, $message);

    }

    /**
     * @param $message array
     * @return TelegramUser
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

    private function handleSteps($user, $message) {
        $text = $message['text'];
        switch ($user->step) {
            case TelegramUser::START_USING:
                if ($text == '/add') {
                    $text = "Укажите профайл: ";
                    $this->sendText($user, $text);
                    $user->step = TelegramUser::ADD_AWAITING;
                    $user->save();
                } else {
                    $keyboard = [
                        ['/add']
                    ];
                    $markup = $this->buildKeyboard($keyboard);

                    $response = Telegram::sendMessage([
                        'chat_id' => $user->t_id,
                        'text' => 'Добавить аккаунт',
                        'reply_markup' => $markup
                    ]);
                }
                break;
            case TelegramUser::ADD_AWAITING:
                if (isset($text) && $text) {
                    $user->step = TelegramUser::ADDED_USER;
                    $user->save();
                    $text = "Вы добавили " . $text;
                    $reply_markup = Telegram::replyKeyboardHide();
                    $response = Telegram::sendMessage([
                        'chat_id' => $user->t_id,
                        'text' => $text,
                        'reply_markup' => $reply_markup
                    ]);
                } else {
                    $text = "Укажите ссылку на профиль пользователя или его id";
                    $this->sendText($user, $text);
                }

                break;
            default:
                break;
        }
    }

    public function sendText($user, $text) {
        Telegram::sendMessage([
            'chat_id' => $user->t_id,
            'text' => $text
        ]);
    }

    private function buildKeyboard($keyboard) {
        return Telegram::replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
    }


    /**
     * @param $message array
     * @return bool
     */
    private function textHandle($message, $user)
    {
        if (!$message)
            return false;

        switch ($message) {
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
