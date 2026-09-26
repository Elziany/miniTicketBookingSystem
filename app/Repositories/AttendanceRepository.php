<?php
namespace App\Repositories;

use App\Models\Attendance;

class AttendanceRepository{

    public function getAttendanceByReservation($reservationId){
       return Attendance::where('reservation_id' , $reservationId)->first();
    }

    public function createAttendance($attendanceData){
        return Attendance::create($attendanceData);
    }
}