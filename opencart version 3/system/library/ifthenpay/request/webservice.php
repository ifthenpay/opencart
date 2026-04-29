<?php

declare(strict_types=1);

namespace Ifthenpay\Request;

class WebService
{
    private string $responseBody = '';
    private int $responseStatusCode = 0;
    private string $responseReasonPhrase = '';

    public function getResponse(): self
    {
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->responseReasonPhrase;
    }

	public function getResponseJson(): array
	{
		$data = json_decode($this->responseBody, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \RuntimeException('JSON decoding failed: ' . json_last_error_msg());
		}

		return $data;
	}

    private function curlExec(string $url, array $options = []): void
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        foreach ($options as $opt => $value) {
            curl_setopt($ch, $opt, $value);
        }

        $result = curl_exec($ch);

        if ($result === false) {
            $error = curl_error($ch);
			if (PHP_VERSION_ID < 80000) { curl_close($ch); }
            throw new \RuntimeException('cURL error: ' . $error);
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $this->responseStatusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->responseBody = substr($result, $headerSize);

        $rawHeaders = substr($result, 0, $headerSize);
        preg_match('/HTTP\/[\d.]+ \d+ (.+)/', $rawHeaders, $matches);
        $this->responseReasonPhrase = isset($matches[1]) ? trim($matches[1]) : '';

		if (PHP_VERSION_ID < 80000) {
        curl_close($ch);
    }
    }

    public function postRequest(string $url, array $data, bool $jsonContentType = false): self
    {
        if ($jsonContentType) {
            $payload = json_encode($data);
            $this->curlExec($url, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $payload,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($payload),
                ],
            ]);
        } else {
            $this->curlExec($url, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data),
            ]);
        }

        return $this;
    }

    public function getRequest(string $url, array $data = []): self
    {
        if (!empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $this->curlExec($url);

        return $this;
    }
}
