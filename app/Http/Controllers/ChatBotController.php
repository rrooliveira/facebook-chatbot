<?php

namespace App\Http\Controllers;

use ChatBot\Domain\Message\Entities\Audio;
use ChatBot\Domain\Message\Entities\File;
use ChatBot\Domain\Message\Entities\Image;
use ChatBot\Domain\Message\Entities\Text;
use ChatBot\Domain\Message\Entities\Video;
use ChatBot\Domain\Message\Services\SenderMessage;
use ChatBot\Domain\Message\Services\WebHook;
use ChatBot\Infrastructure\HttpClient\Guzzle\Guzzle;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function subscribe()
    {
        $webHook = new WebHook();
        $subscribe = $webHook->check(config('chatbotfacebook.validationToken'));

        if (!$subscribe) {
            abort(403, 'Unauthorized Action.');
        }

        return $subscribe;
    }

    public function receiveMessage(Request $request)
    {
        //TO DEBUG VALUES ON HEROKU LOGS
        //file_put_contents("php://stderr", json_encode($request->post()));

        $senderMessage = new SenderMessage();
        //TO RETURN THE MESSAGE, WE NEED TO PUT THE SENDER AS RECIPIENT
        $recipientId = $senderMessage->getSenderId();
        $message = $senderMessage->getMessage();

        $text = new Text($recipientId);
        $file = new File($recipientId);
        $image = new Image($recipientId);
        $audio = new Audio($recipientId);
        $video = new Video($recipientId);
        $httpClient = new Guzzle(config('chatbotfacebook.pageAccessToken'));

        try {
            $text->setMessage('Olá, eu sou o bot...');
            $httpClient->post($text->getMessage());

            $text->setMessage('Você digitou a mensagem abaixo.');
            $httpClient->post($text->getMessage());

            //TEXT
            $text->setMessage($message);
            $httpClient->post($text->getMessage());

//            //FILE
//            $file->setMessage('https://www.php.net/distributions/php-8.1.9.tar.gz');
//            $httpClient->post($file->getMessage());

            //IMAGE
            $image->setMessage('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ4xoX55-BGtjBTQKxa3wDE01Y7-iO-PJV-Y7jJfWFFuYY_xx4XUYXMmb-S5GvRZi2ZW8w&usqp=CAU');
            $httpClient->post($image->getMessage());

//            //AUDIO
//            $audio->setMessage('');
//            $httpClient->post($audio->getMessage());
//
            //VIDEO
            $video->setMessage('https://www.youtube.com/watch?v=_0ppe_koicw');
            $httpClient->post($video->getMessage());

            return '';

        } catch (GuzzleException $exception) {
            var_dump($exception->getMessage());
        }
    }
}
