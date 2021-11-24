<?php

namespace App\Services;

use GuzzleHttp\Client;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    public static function sendMessage($text){
        Telegram::sendMessage([
            'chat_id' => env('TELEGRAM_CHANNEL_ID', '-1001561327890'),
            'parse_mode' => 'HTML',
            'text' => $text
        ]);
    }
}