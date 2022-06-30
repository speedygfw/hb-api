<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use stdClass;

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
            190
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
        $this->logIn($client, "ApiTest2", 'ApiTest2');


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

    public function testCreateTransaction(){
        // $data = '{"amount":0,"name":"aaa","bookingDate":"2022-06-30","type":1,"categories":[],"category":[],"user":{"@id":"/api/users/1"}}';
        $client = static::createClient();
        $user = new stdClass();
        $id = "@id";
        $user->$id = "/api/users/1";

        $data = [
            'amount' => 0,
            'name' => "aaa",
            'bookingDate' => "2022-06-30",
            'type' => 1,
            'user' => $user
        ];
 
       
        $this->logIn($client, $this->loginName, $this->loginPwd);


        $headers = array(
            'ACCEPT' => 'application/ld+json',
            'AUTHORIZATION' => "Bearer " . $this->token,
            'CONTENT_TYPE' => 'application/ld+json',
        );
        $response = $client->request('POST', '/api/bookings',  [
            'body' => json_encode($data),
            'headers' => $headers]);
        $content = json_decode($response->getContent());
        $newId = $content->id;
        return $newId;
        
    }

    /**
     * test deletion of transaction
     * @depends testCreateTransaction
     * @return void
     */
    public function testDeleteTransaction($newId){
        $client = static::createClient();
 
       
        $this->logIn($client, $this->loginName, $this->loginPwd);

        $headers = array(
            'ACCEPT' => 'application/json',
            'AUTHORIZATION' => "Bearer " . $this->token,
            'CONTENT_TYPE' => 'application/json',
        );
        $url = '/api/bookings/' . $newId;
        
        $response = $client->request('DELETE', $url,  [
            'headers' => $headers]);
        $content = json_decode($response->getContent());
        $this->assertResponseIsSuccessful();
    }


}
