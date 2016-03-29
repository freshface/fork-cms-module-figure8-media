<?php

namespace Backend\Modules\Media\Engine;

use Api\V1\Engine\Api as BaseAPI;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Modules\Media\Engine\Model as BackendMediaModel;

/**
 * Api
 *
 * @author Frederik Heyninck <frederik@figure8.be>
 * @author Tommy Van de Velde <tommy@figure8.be>
 */
class Api
{
    // internal constant to enable/disable debugging
    const DEBUG = false;

    // current version
    const VERSION = '1.0.0';

    /**
     * The public key to use
     *
     * @var	string
     */
    private $publicKey;

    /**
     * The private key
     *
     * @var string
     */
    private $privateKey;

    /**
     * The timeout
     *
     * @var int
     */
    private $timeOut = 20;

    /**
     * The user agent
     *
     * @var string
     */
    private $userAgent;
    
    /**
     * The user agent
     *
     * @var string
     */
    private $apiURL = '';


    /**
     * The user agent
     *
     * @var string
     */
    private $apiPort = 80;

    /**
     * Default constructor
     *
     * @return void
     * @param string[optional] $publicKey The public-key of the keypair.
     * @param string[optional] $privateKey The private-key of the keypair.
     */
    public function __construct($publicKey = null, $privateKey = null)
    {
        if ($publicKey !== null) {
            $this->setPublicKey($publicKey);
        }
        if ($publicKey !== null) {
            $this->setPrivateKey($privateKey);
        }
    }


    /**
     * Make the call
     *
     * @return string
     * @param string $method The method to call.
     * @param array[optional] $parameters The parameters to pass.
     * @param bool[optional] $authenticate Should we authenticate?
     * @param bool[optional] $usePOST Should we use POST?
     */
    public function doCall($method, $parameters = array(), $authenticate = true, $usePOST = false)
    {
        if ($this->apiURL == '') {
            throw new Exception('Please provide an API URL.');
        }
        
        // redefine
        $method = (string) $method;
        $parameters = (array) $parameters;
        $authenticate = (bool) $authenticate;

        // add required parameters
        $queryStringParameters['method'] = (string) $method;

        // authentication stuff
        if ($authenticate) {
            // get keys
            $publicKey = $this->getPublicKey();
            $privateKey = $this->getPrivateKey();

            // validate
            if ($publicKey == '' || $privateKey == '') {
                throw new Exception('This method ('. $method .') requires authentication, provide a public and private key.');
            }

            // add prams
            $queryStringParameters['public_key'] = $publicKey;
            $queryStringParameters['nonce'] = time();
            $queryStringParameters['secret'] = md5($publicKey . $privateKey . $queryStringParameters['nonce']);
        }

        // build URL
        $url = $this->apiURL .'/?'. http_build_query($queryStringParameters);

        // use POST?
        if ($usePOST) {
            // set POST
            $options[CURLOPT_POST] = true;

            // add data if needed
            if (!empty($parameters)) {
                $options[CURLOPT_POSTFIELDS] = array('data' => json_encode($parameters));
            }
        } else {
            // any data if needed
            if (!empty($parameters)) {
                // build querystring
                $queryString = http_build_query(array('data' => json_encode($parameters)));

                // prepend
                $url .= '&'. $queryString;
            }
        }

        // set options
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_PORT] = $this->apiPort;
        $options[CURLOPT_USERAGENT] = $this->getUserAgent();
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            $options[CURLOPT_FOLLOWLOCATION] = true;
        }
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_TIMEOUT] = (int) $this->getTimeOut();

        // init
        $curl = curl_init();
        
        // set options
        curl_setopt_array($curl, $options);

        // execute
        $response = curl_exec($curl);
        $headers = curl_getinfo($curl);

        // fetch errors
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        // close
        curl_close($curl);

        // we expect XML so decode it
        $xml = @simplexml_load_string($response, null, LIBXML_NOCDATA);

        // validate XML
        if ($xml === false) {
            throw new Exception('Invalid XML-response.');
        }

        // is error?
        if (!isset($xml['status']) || (string) $xml['status'] != 'ok') {
            // is it a response error
            if (isset($xml->error)) {
                throw new Exception((string) $xml->error);
            }

            // invalid json?
            else {
                throw new Exception('Invalid XML-response.');
            }
        }

        // return
        return $xml;
    }

    /**
     * Get the private key
     *
     * @return string
     */
    private function getPrivateKey()
    {
        return (string) $this->privateKey;
    }

    /**
     * Get the public key
     *
     * @return string
     */
    private function getPublicKey()
    {
        return (string) $this->publicKey;
    }

    /**
     * Get the timeout that will be used
     *
     * @return int
     */
    public function getTimeOut()
    {
        return (int) $this->timeOut;
    }

    /**
     * Get the useragent that will be used. Our version will be prepended to yours.
     * It will look like: "PHP ForkAPI/<version> <your-user-agent>"
     *
     * @return string
     */
    public function getUserAgent()
    {
        return (string) 'PHP ApiCall/'. self::VERSION .' '. $this->userAgent;
    }

    /**
     * Get the private key
     *
     * @return string
     */
    public function setApiURL($url)
    {
        $this->apiURL = rtrim((string) $url, '/');
    }
    
    /**
     * Get the private key
     *
     * @return string
     */
    public function setApiPort($port)
    {
        $this->apiPort = (int) $port;
    }

    /**
     * Set the private key
     *
     * @return void
     * @param string $key The private key.
     */
    public function setPrivateKey($key)
    {
        $this->privateKey = (string) $key;
    }

    /**
     * Set the public key
     *
     * @return void
     * @param string $key The public key.
     */
    public function setPublicKey($key)
    {
        $this->publicKey = (string) $key;
    }

    /**
     * Set the timeout
     * After this time the request will stop. You should handle any errors triggered by this.
     *
     * @return void
     * @param int $seconds The timeout in seconds.
     */
    public function setTimeOut($seconds)
    {
        $this->timeOut = (int) $seconds;
    }

    /**
     * Set the user-agent for you application
     * It will be appended to ours, the result will look like: "PHP ApiCall/<version> <your-user-agent>"
     *
     * @return void
     * @param string $userAgent Your user-agent, it should look like <app-name>/<app-version>.
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string) $userAgent;
    }
}
