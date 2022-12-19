<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Validators\Request\WorkerAllShiftValidator;
use App\Validators\Request\WorkerClockInValidator;
use App\Validators\Request\WorkerClockOutValidator;
use Illuminate\Http\Request;
use App\Contracts\WorkerShiftInterface;
use Illuminate\Http\JsonResponse;

class WorkerShiftController extends Controller
{
    public function __construct(private WorkerShiftInterface $workerShiftRepository){}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function workerClockIn(Request $request): JsonResponse
    {
        if(WorkerClockInValidator::validate($request)) {
            $clockedIn = $this->workerShiftRepository->workerClockIn($request);
            return response()->json($clockedIn);
        }
        return response()->json();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function workerClockOut(Request $request): JsonResponse
    {
        if(WorkerClockOutValidator::validate($request)) {
            $clockedOUt = $this->workerShiftRepository->workerClockOut($request);
            return response()->json($clockedOUt);
        }
        return response()->json();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listOfAllShiftForAWorker(Request $request): JsonResponse
    {
        if(WorkerAllShiftValidator::validate($request)) {
            $workerShifts = $this->workerShiftRepository->listOfAllShiftForAWorker($request);
            return response()->json($workerShifts);
        }
        return response()->json();
    }
}
