<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Contract;
use App\Repository\ContractRepository;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 *
 * @package App\Service
 */
class CollectiveOrderService
{
    private $dFormat = "d.m.Y";

    /**
     * @param  Contract $c
     * @param  DateTime $startDate
     * @param  int      $months
     * @param  int      $turnus
     * @return array
     */
    private function createBookings(Contract $c, DateTime $startDate, int $months, int $turnus): array
    {
        $lastAccomplished = $c->getLastAccomplished();
        $results = [];
        
        $sd = new DateTime($startDate->format($this->dFormat));
        for ($n = 0; $n < $months; $n += $turnus) {
            echo "erzeuge Transaktion " . $sd->format($this->dFormat) . " - " . $c->getRotationAsString() ;

            $res = [
                'contract' => $c,
                'date' => $sd->format($this->dFormat)
            ];

            $sd->add(new DateInterval("P" . $turnus . "M"));
            
            if (!$lastAccomplished || ($sd >= $lastAccomplished)) {
                //echo " lastAccomplished: " . $lastAccomplished->format($this->dFormat);
                echo " -> added... \n";
                $results[] = $res;
            } else {
                echo " -> skipped... \n";
            }

        }

        return $results;
    }

    /**
     * @param  Contract $c
     * @param  DateTime $dt
     * @return array
     */
    public function calcBookings(Contract $c, DateTime $currentDateTime)
    {
        $startDate = $c->getStartDate();
        $accDate = $c->getLastAccomplished();
        
        if ($accDate) {
            echo "currDate:" . $currentDateTime->format($this->dFormat) . "\n";
            echo "accDate:" . $accDate->format($this->dFormat) . "\n";
            $diff = $currentDateTime->diff($accDate);
        } else {
            $diff = $currentDateTime->diff($startDate);
        }

        $endDate = $currentDateTime;

        // if ($accDate && $accDate > $currentDateTime)
        //     $endDate = $accDate;
        
        $results = [];

        echo "years: " . $diff->y . ", Months: " . $diff->m . ", days: " . $diff->days . "\n";
        $months = $diff->y * 12;
        $months += $diff->m;
        echo $c->getName() . ' ' . ", " . $months . " months, startDate: " . $startDate->format($this->dFormat) . ", endDate: " . $endDate->format($this->dFormat) . "\n";
        echo "------------- \n";
        if ($c->getRotation() == Contract::ROT_MONTHLY) {
            $results = $this->createBookings($c, $startDate, $months, 1);
        } elseif ($c->getRotation() == Contract::ROT_QUATERLY) {
            $results = $this->createBookings($c, $startDate, $months, 3);
        } elseif ($c->getRotation() == Contract::ROT_HALFYEARLY) {
            $results = $this->createBookings($c, $startDate, $months, 6);
        }

        echo "\n";
        return $results;
    }

    /**
     * @param  ManagerRegistry    $mr
     * @param  ContractRepository $cr
     * @param  DateTimeInterface  $dt
     * @return int the number of bookings created
     */
    public function do(ManagerRegistry $mr, ContractRepository $cr, DateTimeInterface $dt): int
    {
        $bookingsCreated = 0;
        $contracts = $cr->findAll();
        $today = $dt;

        foreach ($contracts as $c) {
            $bookings = $this->calcBookings($c, $dt);

            //persist bookings
            foreach ($bookings as $booking) {
                $contract = $booking['contract'];
                $dta = $booking['date'];

                $b = new Booking();
                $user = $contract->getUser();
                $b->setUser($user);
                $b->setAmount($contract->getAmount());
                $b->setName($contract->getName());
                $b->setType($contract->getType());
                $b->setBookingDate(new DateTimeImmutable($dta));
                $cats = $contract->getCategories();
                foreach ($cats as $cat) {
                    $b->addCategory($cat);
                }

                $mr->getManager()->persist($b);
                $c->setLastAccomplished($today);
                $mr->getManager()->persist($c);
                $mr->getManager()->flush();
                $bookingsCreated++;
            }
        }

        return $bookingsCreated;
    }
}
