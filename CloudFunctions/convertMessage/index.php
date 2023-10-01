<?php
use CloudEvents\V1\CloudEventInterface;
use Google\CloudFunctions\FunctionsFramework;
use Psr\Http\Message\ServerRequestInterface;
use Predis\Client;
use Google\Cloud\PubSub\PubSubClient;

// Register the function with Functions Framework.
// This enables omitting the `FUNCTIONS_SIGNATURE_TYPE=cloudevent` environment
// variable when deploying. The `FUNCTION_TARGET` environment variable should
// match the first parameter.
FunctionsFramework::cloudEvent('convertMessage', 'convertMessage');

function convertMessage(CloudEventInterface $event): void
{
    $cloudEventData = $event->getData();
    $pubSubData = base64_decode($cloudEventData['message']['data']);

    $message = json_decode($pubSubData, true);
    
    $redis = new Client([
    'scheme' => 'tcp',
    'host'   => 'x.x.x.x',
    'port'   => 6379,
    ]);
    
    $isBot = $redis->get($message['ip']) !== null;
    $isConversion = strpos($message['url'], 'thank-you') !== false;

    $convertedMessage = [
        'key' => $message['key'],
        'timestamp' => date(DATE_ATOM, intval($message['timestamp'] / 1000)),
        'isBot' => $isBot,
        'isConversion' => $isConversion,
    ];

    $pubsub = new PubSubClient();
    $topic = $pubsub->topic('ModifiedKeyEvent');
    $topic->publish(['data' => json_encode($convertedMessage)]);
}