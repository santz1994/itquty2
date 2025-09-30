<?php

namespace App\Services;

class SlackNotifier
{
    protected $webhook;
    protected $enabled;

    public function __construct()
    {
        $this->webhook = env('SLACK_WEBHOOK');
        $this->enabled = env('SLACK_ENABLED', false);
    }

    public function notify(string $message, string $channel = null, string $username = null)
    {
        if (! $this->enabled) {
            return false;
        }

        if (empty($this->webhook)) {
            return false;
        }

        $payload = ['text' => $message];
        if ($channel) $payload['channel'] = $channel;
        if ($username) $payload['username'] = $username;

        $json = json_encode($payload);

        $ch = curl_init($this->webhook);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['payload' => $json]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result !== false;
    }
}
