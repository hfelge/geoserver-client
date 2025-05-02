<?php

namespace Hfelge\GeoServerClient;

class GeoServerException extends \RuntimeException
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $url,
        public readonly string $responseBody
    ) {
        $message = self::extractMessage($responseBody);
        parent::__construct("GeoServer API error {$statusCode} at {$url}: {$message}", $statusCode);
    }

    public static function extractMessage(string $body): string
    {
        if (str_contains($body, '<ows:ExceptionText>')) {
            return strip_tags($body); // quick & safe XML fallback
        }

        $json = json_decode($body, true);
        if (is_array($json)) {
            return $json['message'] ?? $json['error'] ?? json_encode($json);
        }

        return trim($body) ?: 'No error message returned by server';
    }

    public function __toString(): string
    {
        return "[GeoServerException] {$this->getMessage()} (Status {$this->statusCode})";
    }
}
