<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

class BudgetBookApiTest extends ApiTestCase
{
    protected $token = "";

    protected function getDefaultHeaders()
    {
        return [
            'AUTHORIZATION' => "Bearer " . $this->token,
            'CONTENT_TYPE' => 'application/ld+json',
        ];
    }
    
    protected function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }

    public function testNotLoggedIn(): void
    {
        $response = static::createClient()->request('GET', '/api//');

        $this->assertResponseStatusCodeSame(401);
    }

    function logIn($client, $username, $password){
        $response = $client->request('POST', "/api/login_check", ['json' => [
            'username' => $username,
            'password' => $password
        ]]);
        
        $content = json_decode($response->getContent());
        $this->token = $content->token;
        $this->assertResponseIsSuccessful();

    }
}
