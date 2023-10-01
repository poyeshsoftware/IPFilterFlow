<?php
use CloudEvents\V1\CloudEventInterface;
use Google\CloudFunctions\FunctionsFramework;
use Google\Cloud\Storage\StorageClient;

// Register the function with Functions Framework.
// This enables omitting the `FUNCTIONS_SIGNATURE_TYPE=cloudevent` environment
// variable when deploying. The `FUNCTION_TARGET` environment variable should
// match the first parameter.
FunctionsFramework::cloudEvent('saveConvertedMessage', 'saveConvertedMessage');

function saveConvertedMessage(CloudEventInterface $event): void
{
    $cloudEventData = $event->getData();
    $messageData = base64_decode($cloudEventData['message']['data']);

    $message = json_decode($messageData, true);

    $storage = new StorageClient();
    $bucket = $storage->bucket('ip-storage');
    $bucket->upload(
        $messageData,
        [
            'name' => $message['key'] . '.json'
        ]);
}