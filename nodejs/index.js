const TelegramBot = require('node-telegram-bot-api');
const request = require('request');

const token = 'token';
const openWeatherMapApiKey = 'openWeatherMapApiKey'

const bot = new TelegramBot(token, {polling: true});

bot.on('message', (msg) => {
  const chatId = msg.chat.id;

  getCurrentWeather(msg.text, messageText => {
    bot.sendMessage(chatId, messageText);
  });

});

function getCurrentWeather(cityName, callback) {

    encodeCityName = encodeURIComponent(cityName)

    url = `https://api.openweathermap.org/data/2.5/weather?q=${encodeCityName}&units=metric&appid=${openWeatherMapApiKey}`;
    console.log(url)
    request(url, function (error, response, body) {
        if (error) return error

        let info = JSON.parse(body)

        let weatherType = info.weather[0].id; 
        let temp = info.main.temp; 
        let emojiIcon = ''

        if (weatherType >= 200 && weatherType <= 232)  emojiIcon = '⚡';
        else if (weatherType >= 300 && weatherType <= 321) emojiIcon = '☔';
        else if (weatherType >= 500 && weatherType <= 531) emojiIcon = '🌧';
        else if (weatherType >= 600 && weatherType <= 622)  emojiIcon = '❄';
        else if (weatherType >= 701 && weatherType <= 781)  emojiIcon = '🌪';
        else if (weatherType >= 801 && weatherType <= 804)  emojiIcon = '⛅';
        else if (weatherType == 800)  emojiIcon = '☀️';

        text = `Погода в ${cityName}: ${emojiIcon} ${temp}°С`;
        
        callback(text)
    });
}