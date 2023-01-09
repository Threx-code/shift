<?php

namespace App\Contracts;

interface WorkerShiftInterface
{
    public function workerClockIn($request);
    public function workerClockOut($request);
    public function listOfAllShiftForAWorker($request);
    public function shiftManager($request);
    public function dailyRoster($request);
}
