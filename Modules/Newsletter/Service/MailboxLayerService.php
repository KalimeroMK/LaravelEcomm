<?php

declare(strict_types=1);

namespace Modules\Newsletter\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Config;

class MailboxLayerService
{
    protected Client $client;

    protected string $baseUrl = 'http://apilayer.net/api';

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws GuzzleException
     */
    public function check($email): mixed
    {
        $accessKey = Config::get('mailboxlayer.access_key');
        $response = $this->client->get("{$this->baseUrl}/bulk_check", [
            'query' => [
                'access_key' => $accessKey,
                'email' => $email,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
}
