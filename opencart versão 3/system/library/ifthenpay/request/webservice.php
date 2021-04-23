<?php

declare(strict_types=1);

namespace Ifthenpay\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class WebService
{
    private $client;
    private $response;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getResponseJson(): array
    {
        return json_decode(json_encode(json_decode((string) $this->response->getBody())), true);
    }

    public function postRequest(string $url, array $data, bool $jsonContentType = false): self
    {
        try {
            $this->response = $this->client->post(
                $url,
                $jsonContentType ? ['json' => $data] :
                ['form_params' => $data]
            );
            return $this;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getRequest(string $url, array $data = []): self
    {
        try {
            $this->response = $this->client->get($url, ['query' => $data]);
            return $this;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
