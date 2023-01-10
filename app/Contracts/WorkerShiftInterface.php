<?php

namespace App\Contracts;

interface WorkerShiftInterface
{
    /**
     * @param $request
     * @return mixed
     */
    public function workerClockIn($request): mixed;

    /**
     * @param $request
     * @return mixed
     */
    public function workerClockOut($request): mixed;

    /**
     * @param $request
     * @return mixed
     */
    public function listOfAllShiftForAWorker($request): mixed;

    /**
     * @param $request
     * @return mixed
     */
    public function shiftManager($request): mixed;

    /**
     * @param $request
     * @return mixed
     */
    public function dailyRoster($request): mixed;
}
