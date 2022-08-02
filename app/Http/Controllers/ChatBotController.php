<?php

namespace App\Http\Controllers;

use ChatBot\Domain\Message\Entities\Text;
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
        $httpClient = new Guzzle(config('chatbotfacebook.pageAccessToken'));

        try {
            $text->setMessage('Olá, eu sou o bot...');
            $httpClient->post($text->getMessage());

            $text->setMessage('Você digitou a mensagem abaixo.');
            $httpClient->post($text->getMessage());

            $text->setMessage($message);
            $httpClient->post($text->getMessage());

            return '';

        } catch (GuzzleException $exception) {
            var_dump($exception->getMessage());
        }
    }
}
