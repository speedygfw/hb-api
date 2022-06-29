<?php

namespace App\Tests;

use App\Command\HbCronCommand;
use App\Entity\Category;
use App\Entity\Contract;
use App\Entity\User;
use App\Repository\ContractRepository;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
// use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\BufferedOutput;

/** @package App\Tests
 * @group Unit
*/

class CronCommandTest extends TestCase
{

    private function setUpMocks()
    {
        $this->mrMock = $this->getMockBuilder(ManagerRegistry::class)->disableOriginalConstructor()->getMock();
        $this->crMock = $this->getMockBuilder(ContractRepository::class)->disableOriginalConstructor()->getMock();
        $this->crUser = $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
        $this->crManager = $this->getMockBuilder(ObjectManager::class)->disableOriginalConstructor()->getMock();
        $this->cCat = $this->getMockBuilder(Category::class)->disableOriginalConstructor()->getMock();

        $this->crUser
            ->expects($this->any())
            ->method('getId')
        //     ->with($id)
            ->willReturn(1);

        $this->mrMock
            ->expects($this->any())
            ->method("getManager")
            ->willReturn($this->crManager);

        $id = 1;

        $this->crUser->setUsername("testUser");

        $c = new Contract();
        $c->setName("Test");
        $c->setRotation(Contract::ROT_MONTHLY);
        $c->setStartDate(new DateTime("01.01.2022"));
        $c->setAmount(10);
        $c->setType(Contract::INCOME);
        $c->setUser($this->crUser);
        $c->addCategory($this->cCat);

        $this->contracts[] = $c;

        $this->crMock
            ->expects($this->any())
            ->method('findAll')
            ->willReturn($this->contracts);

    }

    protected function setUp(): void
    {
        $this->setUpMocks();

        $application = new Application();
        $application->add(new HbCronCommand($this->mrMock, $this->crMock));

        $this->command = $application->find('hb:cron');
        $this->commandTester = new CommandTester($this->command);

    }

    public function testExecute()
    {

        $this->commandTester->execute([
            'command' => $this->command->getName(),
            'arg1' => "01.02.2022"
        ], []);

        $this->assertEquals('[OK] Booking created: 1', trim($this->commandTester->getDisplay()));
    }
}
