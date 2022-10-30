#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
use Longman\TelegramBot\Request;

$bot_username  = 'openweathermap_php_bot';
$bot_api_key = 'bot_api_key';

while(true) {
    try {
        // Create Telegram API object
        $telegram = new Longman\TelegramBot\Telegram($bot_api_key, $bot_username);
        $telegram->useGetUpdatesWithoutDatabase();

        $server_response = $telegram->handleGetUpdates();

        if($server_response->isOk()) {
            $result = $server_response->getResult();

            foreach($result as $message_item) {
                $message = $message_item->getMessage();

                $message_chat_id = $message->getFrom()->getId();
                $message_text = $message->getText();

                $wether_text = getWeatherText($message_text);

                $result = Request::sendMessage([
                    'chat_id' => $message_chat_id,
                    'text' => 'Ответ: ' . $wether_text
                ]);

                
            }
        }
    } catch (Longman\TelegramBot\Exception\TelegramException $e) {
        // log telegram errors
        echo $e->getMessage();
    }

    sleep(1);
}



function getWeatherText($city_name) {
    $open_weather_map_api_key = 'open_weather_map_api_key';

    $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . $city_name . '&units=metric&appid=' . $open_weather_map_api_key;

    $response = file_get_contents($url);
    $result = json_decode($response, true);

    $temp = $result['main']['temp'];
    $weather_type = $result['weather'][0]['id'];

    $emoji_icon = '';

    if ($weather_type >= 200 && $weather_type <= 232) {
        $emoji_icon = '⚡';
    }
    else if ($weather_type >= 300 && $weather_type <= 321) {
        $emoji_icon = '🌧';
    }
    else if ($weather_type >= 500 && $weather_type <= 531) {
        $emoji_icon = '🌧';
    }
    else if ($weather_type >= 600 && $weather_type <= 622) {
        $emoji_icon = '❄';
    }
    else if ($weather_type >= 701 && $weather_type <= 781) {
        $emoji_icon = '🌪';
    }
    else if ($weather_type >= 801 && $weather_type <= 804) {
        $emoji_icon = '⛅';
    }
    else if ($weather_type == 800) {
        $emoji_icon = '☁';
    }

    $string = 'Погода в ' . $city_name . ': ' . $emoji_icon . $temp.  '°С';

    return $string;
}