<?php

declare(strict_types=1);

namespace Ifthenpay\Request;

use GuzzleHttp\Client;

class WebService
{
    private $client;
    private $response;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getXmlConvertedResponseToArray(): array
    {
        return json_decode(json_encode(json_decode((string) simplexml_load_string($this->response->getBody()->getContents()))[0]), true);
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
            if (get_class($th) === 'InvalidArgumentException' && $th->getMessage() === 'No method can handle the form_params config key') {
                try {
                    $this->response = $this->client->post(
                        $url,
                        $jsonContentType ? ['json' => $data] :
                        ['body' => $data]
                    );
                    return $this;
                } catch (\Throwable $th) {
                    throw $th;
                }
            } else {
                throw $th;
            }
            
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
