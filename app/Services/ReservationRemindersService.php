<?php 
namespace App\Services;

use App\Repositories\ReservationRemindersRepository;

class ReservationRemindersService {
    public function __construct(private ReservationRemindersRepository $reservationRemindersRepoitory)
    {
    }
    public function restRemindersForEvent($eventId){
        $this->reservationRemindersRepoitory->resetRemindersForEvent($eventId);
    }
}