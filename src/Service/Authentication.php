<?php

    declare(strict_types=1);

    namespace IOL\SSO\SDK\Service;

    use IOL\SSO\SDK\Client;
    use IOL\SSO\SDK\Enums\HttpMethod;
    use IOL\SSO\SDK\Exceptions\AuthenticationException;
    use IOL\SSO\SDK\Exceptions\InvalidValueException;
    use JetBrains\PhpStorm\ArrayShape;

    class Authentication
    {
        private Client $client;

        public function __construct(Client $client)
        {
            $this->client = $client;
        }

        /**
         * @throws \IOL\SSO\SDK\Exceptions\AuthenticationException
         * @throws \IOL\SSO\SDK\Exceptions\ResponseException
         * @throws \IOL\SSO\SDK\Exceptions\InvalidValueException
         */
        public function verifyToken(): string
        {
            if($this->client->getAccessToken() === null){
                throw new \IOL\SSO\SDK\Exceptions\AuthenticationException('No Access Token has been provided', 1001);
            }
            $verificationData = $this->client->send(
                url: $this->client::BASE_URI . 'auth/verify',
                data: [],
                method: new HttpMethod(HttpMethod::GET)
            );

            if(isset($verificationData['response']['error'])){
                throw new AuthenticationException('API threw the following error: '.$verificationData['response']['error'][0]['message'], $verificationData['response']['error'][0]['errorCode']);
            }

            if(!isset($verificationData['response']['data']['userId'])){
                throw new InvalidValueException('API did not return the expected data', 8001);
            }

            return $verificationData['response']['data']['userId'];
        }
    }
