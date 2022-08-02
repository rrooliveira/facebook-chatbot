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
        $senderMessage = new SenderMessage();
        $senderId = $senderMessage->getSenderId();
        $recipientId = $senderMessage->getRecipientId();
        $message = $senderMessage->getMessage();

        $text = new Text($recipientId);
        $httpClient = new Guzzle(config('chatbotfacebook.pageAccessToken'));

        try {

            $httpClient->post([$text->message('Olá, eu sou o bot...')]);
            $httpClient->post([$text->message('Você digitou a mensagem abaixo.')]);
            $httpClient->post([$text->message($message)]);

            return '';

        } catch (GuzzleException $exception) {
            var_dump($exception->getMessage());
        }
    }
}
