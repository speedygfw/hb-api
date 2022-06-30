<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use stdClass;

class TransactionApiTest extends BudgetBookApiTest
{
    private $loginName = 'ApiTest1';
    private $loginPwd = "ApiTest1";

    /**
     * provideTransactionData
     * 
     * data: [username, pwd, numOfExpectedItems]
     * @return array<array>
     */
    public function provideTransactionData()
    {
    return ['ApiTest1' =>
        [
            'ApiTest1',
            'ApiTest1',
            187
        ],
        'ApiTest2' => [
            'ApiTest2',
            'ApiTest2',
            1,
        ],
    ];
    }

    /**
     * Summary of provideSingleTransactionData
     * data: [id, name, amount, type]
     * @return array<array>
     */
    public function provideSingleTransactionData()
    {
    return ['1_Gehalt' =>
        [
            1,
            'Gehalt',
            99999,
            0
        ],
        '156_Lebensmittel' => [
            156,
            'Lebensmittel',
            110,
            1
        ],
        '166_Friseur' => [
            166,
            'Friseur',
            25,
            1
        ],
    ];
    }

    /**
    * @dataProvider provideTransactionData
    */
    public function testGetTransactions($userName, $pwd, $numOfExpectedItems): void
    {
        

        $client = static::createClient();
        $this->logIn($client, $userName, $pwd);

        $headers = $this->getDefaultHeaders();
        $response = $client->request('GET', '/api/bookings',  ['headers' => $headers]);
        $content = json_decode($response->getContent());

        $n = "hydra:totalItems";
        $this->assertSame($numOfExpectedItems, $content->$n);
        
    }

    
    /**
     * testSingleTransaction
     * @dataProvider provideSingleTransactionData
     * @return void
     */
    public function testSingleTransaction($id, $name, $amount, $type): void
    {
        $client = static::createClient();
        $this->logIn($client, $this->loginName, $this->loginPwd);


        $headers = $this->getDefaultHeaders();

        $response = $client->request('GET', '/api/bookings/' . $id,  ['headers' => $headers]);

        $content = json_decode($response->getContent());
        $this->assertSame($name, $content->name);
        $this->assertSame($amount, $content->amount);
        $this->assertSame($type, $content->type);
    }

    public function testSingleTransactionNotFound()
    {
        $client = static::createClient();
        $this->logIn($client, "ApiTest2", 'ApiTest2');
        $headers = $this->getDefaultHeaders();
        $response = $client->request('GET', '/api/bookings/1',  ['headers' => $headers]);

        $this->expectExceptionMessage("Not Found");
        $response->getHeaders();
    }

    public function testGetTransaction_401(){
        $client = static::createClient();
        $client->request('GET', '/api/bookings');
        $this->assertResponseStatusCodeSame(401);
    }

    private function getCreateTransactionData()
    {
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

        return $data;

    }
    public function testCreateTransaction(){
        $client = static::createClient();
        $data = $this->getCreateTransactionData();
 
       
        $this->logIn($client, $this->loginName, $this->loginPwd);


        $headers = $this->getDefaultHeaders();
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

        $headers = $this->getDefaultHeaders();
        $url = '/api/bookings/' . $newId;
        
        $client->request('DELETE', $url,  ['headers' => $headers]);

        $this->assertResponseIsSuccessful();
    }


}
