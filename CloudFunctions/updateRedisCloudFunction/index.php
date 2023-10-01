<?php

use Google\CloudFunctions\FunctionsFramework;
use Psr\Http\Message\ServerRequestInterface;
use Predis\Client;

// Register the function with Functions Framework.
// This enables omitting the `FUNCTIONS_SIGNATURE_TYPE=http` environment
// variable when deploying. The `FUNCTION_TARGET` environment variable should
// match the first parameter.
FunctionsFramework::http('updateRedisCloudFunction', 'updateRedisCloudFunction');

function updateRedisCloudFunction(ServerRequestInterface $request): string
{

    $url = 'https://antoinevastel.com/data/avastel-longtime-bot-ips.txt';

    $content = file_get_contents($url);

    // Check and log the content's status
    if (!$content) {
        error_log("Failed to fetch content from: $url");
        return "Failed to fetch content";
    }

    $lines = explode("\n", $content);

    $redis = new Client([
        'scheme' => 'tcp',
        'host' => 'x.x.x.x',
        'port' => 6379,
    ]);

    // Clear previous IPs
    $redis->flushdb();


    foreach ($lines as $line) {
        $parts = explode(";", $line);
        if (filter_var($parts[0], FILTER_VALIDATE_IP)) {
            $redis->set($parts[0], true);
        }
    }

    return 'Completed';
}