<?php

    use IOL\SSO\SDK\Client;
    use IOL\SSO\SDK\Enums\HttpMethod;
    use JetBrains\PhpStorm\ArrayShape;

    class User
    {
        public const ENDPOINT_URL = 'user/info';

        private Client $client;

        public function __construct(Client $client)
        {
            $this->client = $client;
        }

        /**
         * @throws \IOL\SSO\SDK\Exceptions\AuthenticationException
         * @throws \IOL\SSO\SDK\Exceptions\ResponseException
         */
        #[ArrayShape(['httpCode' => "int", 'response' => "array"])]
        public function getUserInfo(): array
        {
            if($this->client->getAccessToken() === null){
                throw new \IOL\SSO\SDK\Exceptions\AuthenticationException('No Access Token has been provided', 1001);
            }
            return $this->client->send(
                url: $this->client::BASE_URI . 'user/info',
                data: [],
                method: new HttpMethod(HttpMethod::GET)
            );
        }
    }
