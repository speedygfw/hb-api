<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;



class TransactionApiTest extends BudgetBookApiTest
{
    private $loginName = 'ApiTest1';
    private $loginPwd = "ApiTest1";

    public function provideTransactionData()
    {
    return ['ApiTest1' =>
        [
            'ApiTest1',
            'ApiTest1',
            169
        ],
        'ApiTest2' => [
            'ApiTest2',
            'ApiTest2',
            1,
        ],
    ];
    }

    /**
    * @dataProvider provideTransactionData
    */
    public function testGetTransactions($userName, $pwd, $expectedItems): void
    {
        

        $client = static::createClient();
        $this->logIn($client, $userName, $pwd);


        $headers = array(
            'AUTHORIZATION' => "Bearer " . $this->token,
            'CONTENT_TYPE' => 'application/json',
            "ACCEPT" => 'application/ld+json'
        );


        $response = $client->request('GET', '/api/bookings',  ['headers' => $headers]);
        $content = json_decode($response->getContent());

        $n = "hydra:totalItems";
        $this->assertSame($expectedItems, $content->$n);
        
        

    }

    public function testSingleTransaction(): void
    {
        $client = static::createClient();
        $this->logIn($client, $this->loginName, $this->loginPwd);


        $headers = array(
            'AUTHORIZATION' => "Bearer " . $this->token,
            'CONTENT_TYPE' => 'application/json',
        );


        $response = $client->request('GET', '/api/bookings/1',  ['headers' => $headers]);

        $content = json_decode($response->getContent());
        $this->assertSame("Gehalt", $content->name);
    }

    public function testSingleTransactionWrongUser()
    {
        $client = static::createClient();
        $this->logIn($client, 'johanna', 'drack2020');


        $headers = array(
            'AUTHORIZATION' => "Bearer " . $this->token,
            'CONTENT_TYPE' => 'application/json',
        );


        $response = $client->request('GET', '/api/bookings/1',  ['headers' => $headers]);



        $this->expectExceptionMessage("Not Found");
        $response->getHeaders();
    }

    public function testGetTransaction_401(){
        $client = static::createClient();
        $response = $client->request('GET', '/api/bookings');
        $this->assertResponseStatusCodeSame(401);
    }


}
