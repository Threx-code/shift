<?php

namespace App\Repositories;

use App\Contracts\WorkerShiftInterface;
use App\Services\WorkerShiftService;
use \Illuminate\Http\JsonResponse;
use JsonException;


class WorkerShiftRepository implements WorkerShiftInterface
{
    private WorkerShiftService $workerShiftService;

    public function __construct()
    {
       $this->workerShiftService = new WorkerShiftService();
    }

    /**
     * @param $request
     * @return array
     */
    public function workerClockIn($request): array
    {
        return $this->workerShiftService->workerClockIn($request);
    }


    /**
     * @param $request
     * @return mixed
     */
    public function workerClockOut($request): mixed
    {
        return $this->workerShiftService->workerClockOut($request);
    }

    public function listOfAllShiftForAWorker($request)
    {
        return $this->workerShiftService->listOfAllShiftForAWorker($request);
    }

    public function shiftsAWorkerDidNotWork($request)
    {
        return $this->workerShiftService->shiftsAWorkerDidNotWork($request);
    }
}
