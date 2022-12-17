<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Validators\Request\AllOrderValidator;
use App\Validators\Request\Autocomplete;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Contracts\WorkerShiftInterface;
use Illuminate\Http\JsonResponse;

class WorkerShiftController extends Controller
{
    private WorkerShiftInterface  $orderRepository;

    public function __construct(WorkerShiftInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllOrders(Request $request): JsonResponse
    {
        $allOrder = $this->orderRepository->getAllOrders($request);
        return response()->json($allOrder['orders']);
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function getAllOrderView(Request $request): Factory|View|Application
    {
        $orders = $this->orderRepository->getAllOrders($request);
        $data = $orders['data'];
        $orders = $orders['orders'];
        return view('orders', compact('orders', 'data'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function topDistributors(Request $request): JsonResponse
    {
        $topDistributors = $this->orderRepository->topDistributors($request);
        return response()->json($topDistributors);
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function getTopDistributorsView(Request $request): Factory|View|Application
    {
        $distributors = $this->orderRepository->topDistributors($request);
        return view('distributors', compact('distributors', ));
    }


    public function autocomplete(Request $request)
    {
        if(Autocomplete::validate($request)) {
            return $this->orderRepository->autocomplete($request);
        }
    }


    /**
     * @param Request $request
     * @return Application|Factory|View|void
     */
    public function search(Request $request)
    {
        if(AllOrderValidator::validate($request)){
            $orders = $this->orderRepository->getAllOrders($request);
            $data = $orders['data'];
            $orders = $orders['orders'];
            return view('search', compact('orders', 'data'));
        }

    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function search2(Request $request)
    {
        if(AllOrderValidator::validate($request)){
            $orders = $this->orderRepository->getAllOrders($request);
            $data = $orders['data'];
            $orders = $orders['orders'];
            return response()->json($orders);
        }

    }

}
