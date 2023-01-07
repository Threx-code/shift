<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkerRequest;
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
     * @param WorkerRequest $request
     * @return JsonResponse
     */
    public function shiftManager(WorkerRequest $request): JsonResponse
    {
        $clockedIn = $this->workerShiftRepository->shiftManager($request);
            return response()->json($clockedIn);
    }

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
     * @param array $workerShifts
     * @return JsonResponse
     */
    public function listOfAllShiftForAWorker(Request $request, array $workerShifts = []): JsonResponse
    {
        if(WorkerAllShiftValidator::validate($request)) {
            $workerShifts = $this->workerShiftRepository->listOfAllShiftForAWorker($request);
        }
        return response()->json($workerShifts);
    }
}
