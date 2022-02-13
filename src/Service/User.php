<?php

    declare(strict_types=1);

    namespace IOL\SSO\SDK\Service;

    use IOL\SSO\SDK\Client;
    use IOL\SSO\SDK\Enums\HttpMethod;
    use IOL\SSO\SDK\Exceptions\AuthenticationException;
    use IOL\SSO\SDK\Exceptions\ResponseException;
    use JetBrains\PhpStorm\ArrayShape;

    class User
    {
        private Client $client;

        public function __construct(Client $client)
        {
            $this->client = $client;
        }

        /**
         * @throws AuthenticationException
         * @throws ResponseException
         */
        #[ArrayShape(['httpCode' => "int", 'response' => "array"])]
        public function getUserInfo(?string $userId): array
        {
            return $this->client->send(
                url: $this->client::BASE_URI . 'user/info'. (is_null($userId) ? '' : '?userId='.$userId),
                data: [],
                method: new HttpMethod(HttpMethod::GET)
            );
        }

        /**
         * @throws AuthenticationException
         * @throws ResponseException
         */
        #[ArrayShape(['httpCode' => "int", 'response' => "array"])]
        public function getList(): array
        {
            return $this->client->send(
                url: $this->client::BASE_URI . 'user/list',
                data: [],
                method: new HttpMethod(HttpMethod::GET)
            );
        }
    }
