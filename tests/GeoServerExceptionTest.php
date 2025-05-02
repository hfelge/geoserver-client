<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Hfelge\GeoServerClient\GeoServerException;

class GeoServerExceptionTest extends TestCase
{
    #[Test]
    public function it_creates_exception_from_text_body(): void
    {
        $e = new GeoServerException(400, '/rest/test', 'Bad request');

        $this->assertSame(400, $e->statusCode);
        $this->assertSame('/rest/test', $e->url);
        $this->assertSame('Bad request', $e->responseBody);
        $this->assertStringContainsString('Bad request', $e->getMessage());
    }

    #[Test]
    public function it_creates_exception_from_json_body(): void
    {
        $body = json_encode(['message' => 'Invalid SLD file']);
        $e = new GeoServerException(500, '/rest/styles', $body);

        $this->assertStringContainsString('Invalid SLD file', $e->getMessage());
    }

    #[Test]
    public function it_handles_empty_body_gracefully(): void
    {
        $e = new GeoServerException(404, '/rest/featuretypes/missing', '');

        $this->assertStringContainsString('No error message', $e->getMessage());
    }
}
