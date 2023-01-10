<?php

namespace App\Repositories;

use App\Contracts\WorkerShiftInterface;
use App\Services\WorkerShiftService;
use \Illuminate\Http\JsonResponse;
use JsonException;


class WorkerShiftRepository implements WorkerShiftInterface
{
    /**
     * @var WorkerShiftService
     */
    private WorkerShiftService $workerShiftService;

    public function __construct()
    {
       $this->workerShiftService = new WorkerShiftService();
    }

    /**
     * @param $request
     * @return bool[]|null
     */
    public function workerClockIn($request): ?array
    {
        return $this->workerShiftService->workerClockIn($request);
    }


    /**
     * @param $request
     * @return bool[]|null
     */
    public function workerClockOut($request): ?array
    {
        return $this->workerShiftService->workerClockOut($request);
    }

    /**
     * @param $request
     * @return array|int[]|mixed|null
     */
    public function listOfAllShiftForAWorker($request): mixed
    {
        return $this->workerShiftService->listOfAllShiftForAWorker($request);
    }

    /**
     * @param $request
     * @return bool[]|string[]|null
     */
    public function shiftManager($request): ?array
    {
        return $this->workerShiftService->shiftManager($request);
    }

    /**
     * @param $request
     * @return bool[]|string[]|null
     */
    public function dailyRoster($request): ?array
    {
        return $this->workerShiftService->dailyRoster($request);
    }
}
