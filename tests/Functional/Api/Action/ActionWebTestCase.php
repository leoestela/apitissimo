<?php


namespace App\Tests\Functional\Api\Action;


use App\Tests\Functional\FunctionalWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ActionWebTestCase extends FunctionalWebTestCase
{
    protected function doRequest(string $method, string $uri, $payload = null): Response
    {
        $client = static::createClient();
        $client->request(
            $method,
            $uri,
            [],
            [],
            [],
            json_encode($payload),
            true);

        return $client->getResponse();
    }
}