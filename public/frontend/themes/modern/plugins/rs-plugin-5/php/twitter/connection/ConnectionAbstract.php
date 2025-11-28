<?php

declare(strict_types=1);

namespace TwitterPhp\Connection;

/**
 * Class Base
 */
abstract class Base
{
    /**
     * Url for Twitter api
     */
    public const TWITTER_API_URL = 'https://api.twitter.com';

    /**
     * Twitter URL that authenticates bearer tokens
     */
    public const TWITTER_API_AUTH_URL = 'https://api.twitter.com/oauth2/token/';

    /**
     * Version of Twitter api
     */
    public const TWITTER_API_VERSION = '1.1';

    /**
     * Timeout value for curl connections
     */
    public const DEFAULT_TIMEOUT = 10;

    /**
     * METHOD GET
     */
    public const METHOD_GET = 'GET';

    /**
     * METHOD POST
     */
    public const METHOD_POST = 'POST';

    /**
     * @param  string  $url
     * @return array
     */
    abstract protected function _buildHeaders($url, ?array $parameters, $method);

    /**
     * Do GET request to Twitter api
     *
     * @link https://dev.twitter.com/docs/api/1.1
     *
     * @return mixed
     */
    final public function get($resource, array $parameters = [])
    {
        $url = $this->_prepareUrl($resource);
        $headers = $this->_buildHeaders($url, $parameters, self::METHOD_GET);
        $url = $url.'?'.http_build_query($parameters);
        $curlParams = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
        ];

        return $this->_callApi($curlParams);
    }

    /**
     * Do POST request to Twitter api
     *
     * @link https://dev.twitter.com/docs/api/1.1
     *
     * @return mixed
     */
    final public function post($resource, array $parameters = [])
    {
        $url = $this->_prepareUrl($resource);
        $headers = $this->_buildHeaders($url, $parameters, self::METHOD_POST);
        $curlParams = [
            CURLOPT_URL => $url,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $parameters,
            CURLOPT_HTTPHEADER => $headers,
        ];

        return $this->_callApi($curlParams);
    }

    /**
     * Call Twitter api
     *
     * @return array
     */
    protected function _callApi(array $params)
    {
        $curl = curl_init();
        curl_setopt_array($curl, $params);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, self::DEFAULT_TIMEOUT);
        $response = curl_exec($curl);

        return json_decode($response, true);
    }

    /**
     * @param  string  $resource
     * @return string
     */
    private function _prepareUrl($resource)
    {
        return self::TWITTER_API_URL.'/'.self::TWITTER_API_VERSION.'/'.mb_ltrim($resource, '/').'.json';
    }
}
