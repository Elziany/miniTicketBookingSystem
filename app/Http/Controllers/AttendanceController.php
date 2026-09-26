<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequest;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        private AttendanceService $attendanceService
    ) {
    }

    public function checkIn(AttendanceRequest $request): JsonResponse
    {
        $attendance = $this->attendanceService->checkInByQr(
            $request['reservation_reference'],
            $request->user()->id
        );
        return response()->json([
            'message' => 'Guest checked in successfully.',
            'data' => $attendance,
        ], 201);
    }
}