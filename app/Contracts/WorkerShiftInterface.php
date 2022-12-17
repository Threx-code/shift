<?php

namespace App\Contracts;

interface WorkerShiftInterface
{
    public function workerClockIn($request);
    public function workerClockOut($request);
    public function listOfAllShiftForAWorker($request);
    public function shiftsAWorkerDidNotWork($request);
}
