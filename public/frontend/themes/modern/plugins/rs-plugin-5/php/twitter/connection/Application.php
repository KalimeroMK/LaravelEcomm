<?php

declare(strict_types=1);

namespace TwitterPhp\Connection;

use TwitterPhp\RestApiException;

class Application extends Base
{
    /**
     * @var string
     */
    private $_consumerKey;

    /**
     * @var string
     */
    private $_consumerSecret;

    /**
     * @var string
     */
    private $_bearersToken = null;

    /**
     * @param  string  $consumerKey
     * @param  string  $consumerSecret
     */
    public function __construct($consumerKey, $consumerSecret)
    {
        $this->_consumerKey = $consumerKey;
        $this->_consumerSecret = $consumerSecret;
    }

    /**
     * @param  string  $url
     * @return array
     */
    protected function _buildHeaders($url, ?array $parameters, $method)
    {
        return $headers = [
            'Authorization: Bearer '.$this->_getBearerToken(),
        ];
    }

    /**
     * Get Bearer token
     *
     * @link https://dev.twitter.com/docs/auth/application-only-auth
     *
     * @return string
     *
     * @throws RestApiException
     */
    private function _getBearerToken()
    {
        if (! $this->_bearersToken) {
            $token = urlencode($this->_consumerKey).':'.urlencode($this->_consumerSecret);
            $token = base64_encode($token);

            $headers = [
                'Authorization: Basic '.$token,
            ];

            $options = [
                CURLOPT_URL => self::TWITTER_API_AUTH_URL,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            ];

            $response = $this->_callApi($options);

            if (isset($response['token_type']) && $response['token_type'] === 'bearer') {
                $this->_bearersToken = $response['access_token'];
            } else {
                throw new RestApiException('Error while getting access token');
            }
        }

        return $this->_bearersToken;
    }
}
