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

        echo env('FB_VALIDATION_TOKEN') . '<br>';
        echo $subscribe . ' - SUBSCRIBE';
        die();

        if (!$subscribe) {
            abort(403, 'Unauthorized Action.');
        }

        return response()->json($subscribe);
    }

    public function receiveMessage(Request $request)
    {
        $senderMessage = new SenderMessage();
        $senderId = $senderMessage->getSenderId();
        $message = $senderMessage->getMessage();

        $text = new Text($senderId);
        $httpClient = new Guzzle(config('chatbotfacebook.pageAccessToken'));

        try {

            $httpClient->post(['message' => $text->message('Olá, eu sou o bot...')]);
            $httpClient->post(['message' => $text->message('Você digitou a mensagem abaixo.')]);
            $httpClient->post(['message' => $text->message($message)]);

            return '';

        } catch (GuzzleException $exception) {
            var_dump($exception->getMessage());
        }
    }
}
