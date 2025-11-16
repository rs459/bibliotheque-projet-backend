<?php

namespace App\Tests\Service;

use App\Service\GoogleBooksApiService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GoogleBooksApiServiceTest extends TestCase
{
    public function testSearchBooksReturnsResults(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn([
            'items' => [
                [
                    'id' => '123',
                    'volumeInfo' => [
                        'title' => 'Test Book',
                        'authors' => ['Test Author'],
                        'publisher' => 'Test Publisher',
                        'publishedDate' => '2024-01-01',
                        'description' => 'Test description',
                        'pageCount' => 200,
                        'industryIdentifiers' => [
                            ['type' => 'ISBN_13', 'identifier' => '9781234567890']
                        ],
                        'imageLinks' => [
                            'thumbnail' => 'http://example.com/image.jpg'
                        ]
                    ]
                ]
            ]
        ]);

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $mockLogger = $this->createMock(LoggerInterface::class);

        $service = new GoogleBooksApiService($mockHttpClient, $mockLogger);
        $results = $service->searchBooks('test query');

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertSame('Test Book', $results[0]['title']);
        $this->assertSame(['Test Author'], $results[0]['authors']);
    }

    public function testSearchBooksWithEmptyQuery(): void
    {
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $service = new GoogleBooksApiService($mockHttpClient, $mockLogger);
        $results = $service->searchBooks('');

        $this->assertIsArray($results);
        $this->assertEmpty($results);
    }

    public function testSearchBooksWithApiKey(): void
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('toArray')->willReturn(['items' => []]);

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->stringContains('key=test-api-key')
            )
            ->willReturn($mockResponse);

        $mockLogger = $this->createMock(LoggerInterface::class);

        $service = new GoogleBooksApiService($mockHttpClient, $mockLogger);
        $service->searchBooks('test', 'test-api-key');
    }
}
