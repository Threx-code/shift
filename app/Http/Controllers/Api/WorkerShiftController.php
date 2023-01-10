<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DailyRosterRequest;
use App\Http\Requests\WorkerClockAllShiftRequest;
use App\Http\Requests\WorkerClockInRequest;
use App\Http\Requests\WorkerClockOutRequest;
use App\Http\Requests\WorkerRequest;
use Illuminate\Http\Request;
use App\Contracts\WorkerShiftInterface;
use Illuminate\Http\JsonResponse;

class WorkerShiftController extends Controller
{
    public function __construct(private WorkerShiftInterface $workerShiftRepository){}

    /**
     * @param WorkerRequest $request
     * @return JsonResponse
     */
    public function shiftManager(WorkerRequest $request): JsonResponse
    {
            return response()->json($this->workerShiftRepository->shiftManager($request));
    }

    /**
     * @param DailyRosterRequest $request
     * @return JsonResponse
     */
    public function dailyRoster(DailyRosterRequest $request): JsonResponse
    {
        return response()->json($this->workerShiftRepository->dailyRoster($request));
    }

    /**
     * @param WorkerClockInRequest $request
     * @return JsonResponse
     */
    public function workerClockIn(WorkerClockInRequest $request): JsonResponse
    {
        return response()->json($this->workerShiftRepository->workerClockIn($request));
    }


    /**
     * @param WorkerClockOutRequest $request
     * @return JsonResponse
     */
    public function workerClockOut(WorkerClockOutRequest $request): JsonResponse
    {
        return response()->json($this->workerShiftRepository->workerClockOut($request));
    }

    /**
     * @param WorkerClockAllShiftRequest $request
     * @return JsonResponse
     */
    public function listOfAllShiftForAWorker(WorkerClockAllShiftRequest $request): JsonResponse
    {
        return response()->json($this->workerShiftRepository->listOfAllShiftForAWorker($request));
    }


}
