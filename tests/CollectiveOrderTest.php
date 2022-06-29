<?php

namespace App\Tests;

// use PHPUnit\Framework\TestCase;

use App\Entity\Contract;
use App\Service\CollectiveOrderService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CollectiveOrderTest extends KernelTestCase
{
     /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $contract;
    // private $contractQuaterly;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

            $this->contract = $this->em->getRepository(Contract::class)->findBy(['name' => 'Gehalt'])[0];
            // $this->contractQuaterly = $this->em->getRepository(Contract::class)->findBy(['name' => 'GEZ']);
            $this->assertNotNull($this->contract);
    }

    public function testSearchFindAll()
    {
        $cRep = $this->em->getRepository(Contract::class)->findAll();

        // $this->assertSame(9, count($cRep));

    }

    public function testCalcBookings_Monthly_Year()
    {

        $dt = new DateTime("01.01.2023");

        $year = date("Y");
        for ($n = 0; $n < 12; $n++) {
            $nr = str_pad($n + 1, 1, 0, STR_PAD_LEFT);
            $expectedDates[] = (new DateTime("01." . $nr . "." . $year))->format("d.m.Y");
        }
        // var_dump($expectedDates);

        $co = new CollectiveOrderService($this->contract, $dt);
        $bookings = $co->calcBookings($this->contract, $dt);
        $this->assertEquals(12, count($bookings));

        for($n = 0; $n < count($bookings); $n++)
        {
            $b = $bookings[$n];
            $this->assertSame($b['date'], $expectedDates[$n]);
        }

    }

    public function testCalcBookings_Monthly_LastAccomplished()
    {
        $dt = new DateTime("01.06.2022");
        $co = new CollectiveOrderService($this->contract, $dt);
   
        $this->contract->setLastAccomplished(new DateTime("01.01.2022"));
        $bookings = $co->calcBookings($this->contract, $dt);
        $this->assertEquals(5, count($bookings));

    }
    public function testCalcBookings_Monthly_LastAccomplished_Null()
    {
        $dt = new DateTime("01.06.2022");
        $co = new CollectiveOrderService($this->contract, $dt);
   
        $this->contract->setLastAccomplished(null);
        $bookings = $co->calcBookings($this->contract, $dt);
        $this->assertEquals(5, count($bookings));

    }

    public function testCalcBookings_Quaterly_Year()
    {

        $dt = new DateTime("01.01.2023");

        $year = date("Y");
        for ($n = 0; $n < 12; $n += 3) {
            $nr = str_pad($n + 1, 1, 0, STR_PAD_LEFT);
            $expectedDates[] = (new DateTime("01." . $nr . "." . $year))->format("d.m.Y");
        }

        $this->contract->setRotation(Contract::ROT_QUATERLY);

        $co = new CollectiveOrderService($this->contract, $dt);
        $bookings = $co->calcBookings($this->contract, $dt);
        $this->assertEquals(4, count($bookings));

        for($n = 0; $n < count($bookings); $n++)
        {
            $b = $bookings[$n];
            $this->assertSame($b['date'], $expectedDates[$n]);
        }

    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->em->close();
        $this->em = null;
    }
}
