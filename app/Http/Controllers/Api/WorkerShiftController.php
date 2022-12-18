<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Validators\Request\AllOrderValidator;
use App\Validators\Request\Autocomplete;
use App\Validators\Request\WorkerClockInValidator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
        $clockedOUt = $this->workerShiftRepository->workerClockOut($request);
        return response()->json($clockedOUt);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function listOfAllShiftForAWorker(Request $request): JsonResponse
    {
        if(Autocomplete::validate($request)) {
            return $this->workerShiftRepository->listOfAllShiftForAWorker($request);
        }
        return response()->json();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function shiftsAWorkerDidNotWork(Request $request): JsonResponse
    {
        if(AllOrderValidator::validate($request)){
            $shiftsWorkerDidNotWork = $this->workerShiftRepository->shiftsAWorkerDidNotWork($request);
            return response()->json($shiftsWorkerDidNotWork);
        }
        return response()->json();

    }

}
