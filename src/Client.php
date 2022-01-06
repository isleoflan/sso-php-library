<?php

    declare(strict_types=1);

    namespace IOL\SSO\SDK;

    use JetBrains\PhpStorm\ArrayShape;
    use IOL\SSO\SDK\Enums\HttpMethod;
    use IOL\SSO\SDK\Exceptions\AuthenticationException;
    use IOL\SSO\SDK\Exceptions\ResponseException;

    class Client
    {
        public const BASE_URI = 'https://api.sso.isleoflan.ch/v1';

        public function __construct(
            private string $appToken,
            private string $apiKey,
        )
        {
        }

        /**
         * @throws \IOL\SSO\SDK\Exceptions\ResponseException
         * @throws \IOL\SSO\SDK\Exceptions\AuthenticationException
         */
        #[ArrayShape(['httpCode' => "int", 'response' => "array"])]
        public function send(string $url, array $data, HttpMethod $method): array
        {
            $url = match ($method->jsonSerialize()) {
                HttpMethod::DELETE, HttpMethod::GET => $data !== [] ? $url.http_build_query($data) : $url,
                default => $url
            };
            $apiRequest = curl_init($url);
            curl_setopt($apiRequest, CURLOPT_HTTPHEADER, [
                               "Authorization: Bearer " . $this->getApiKey(),
                               "Iol-App-Token: ".$this->getAppToken(),
                               "Content-Type: application/json"
                           ]
            );
            switch($method->jsonSerialize()){
                case HttpMethod::POST:
                    curl_setopt($apiRequest, CURLOPT_POST, true);
                    curl_setopt($apiRequest, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
                case HttpMethod::DELETE:
                case HttpMethod::PATCH:
                case HttpMethod::PUT:
                    curl_setopt($apiRequest, CURLOPT_CUSTOMREQUEST, $method->jsonSerialize());
                    curl_setopt($apiRequest, CURLOPT_POSTFIELDS, json_encode($data));
                    break;
            }

            curl_setopt($apiRequest, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($apiRequest);
            $responseCode = curl_getinfo($apiRequest, CURLINFO_HTTP_CODE);

            if(json_encode(json_decode($response, true)) !== $response){
                throw new ResponseException('API returned invalid JSON response', 9901);
            }
            switch($responseCode){
                case 400:
                    throw new ResponseException('The sent request could not be understood by the API.', 9902);
                case 401:
                    throw new AuthenticationException('Could not authenticate. Please check your API key and App Token.', 9903);
                case 404:
                    throw new ResponseException('The requested resource could not be found by the API.', 9904);
            }

            return ['httpCode' => $responseCode, 'response' => json_decode($response, true)];
        }

        /**
         * @return string
         */
        private function getApiKey(): string
        {
            return $this->apiKey;
        }

        /**
         * @param string $apiKey
         */
        public function setApiKey(string $apiKey): void
        {
            $this->apiKey = $apiKey;
        }

        /**
         * @return string
         */
        public function getAppToken(): string
        {
            return $this->appToken;
        }

        /**
         * @param string $pageId
         */
        public function setAppToken(string $appToken): void
        {
            $this->appToken = $appToken;
        }


    }
