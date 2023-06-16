<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\UrlRepository;
use App\Entity\Url;

class UrlControllerTest extends BaseApiTestCase
{
    public string $url = "https://google.com";

    public function createUrl() {
        $client = static::createClient();

        $response = $client->request('GET', "/encode-url?url={$this->url}");

        $this->assertResponseIsSuccessful();

        return $client;
    }

    public function getHash() {
        $urlEntity = $this->em
            ->getRepository(Url::class)
            ->findOneByUrl($this->url);

        return $urlEntity->getHash();
    }

    public function testEncodeUrl(): void
    {
        $this->createUrl();

        $this->assertJsonContains([
            'hash' => $this->getHash(),
        ]);
    }

    public function testEncodeUrlValidation(): void
    {
        $client = static::createClient();

        $response = $client->request('GET', "/encode-url?url=123123");

        $this->assertResponseStatusCodeSame(400);
    }

    public function testDecodeUrl(): void
    {
        $client = $this->createUrl();

        $client->request('GET', "/decode-url?hash={$this->getHash()}");

        $this->assertResponseIsSuccessful();

        $this->assertJsonContains([
            'url' => $this->url,
        ]);
    }

    public function testDecodeUrlValidation(): void
    {
        $client = $this->createUrl();

        $client->request('GET', "/decode-url?hash=123123");

        $this->assertResponseStatusCodeSame(400);
    }

    public function testGoUrl(): void
    {
        $client = $this->createUrl();

        $client->request('GET', "/go-url?hash={$this->getHash()}");

        $this->assertResponseRedirects($this->url);
    }

    public function testGoUrlValidation(): void
    {
        $client = $this->createUrl();

        $client->request('GET', "/go-url?hash=123123");

        $this->assertResponseStatusCodeSame(400);
    }
}
