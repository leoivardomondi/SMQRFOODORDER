<?php

namespace App\Http\Controllers\Frontend;


use Exception;
use App\Models\Order;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Http\Resources\DeliveryBoyOrderCountResource;
use App\Http\Resources\SimpleDeliveryBoyOrderResource;

class DeliveryBoyOrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return SimpleDeliveryBoyOrderResource::collection($this->orderService->deliveryBoyOrder($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Order $order): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $isAssigned = $order->delivery_boy_id === $user->id;
        $isOpenDelivery = $order->order_type === \App\Enums\OrderType::DELIVERY 
            && (!$order->delivery_boy_id || $order->delivery_boy_id == 0)
            && $order->status === \App\Enums\OrderStatus::ACCEPT;

        abort_unless($isAssigned || $isOpenDelivery, 403, 'Unauthorized');

        try {
            return new OrderDetailsResource($this->orderService->deliveryBoyOrderDetails($order));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function claim(Order $order): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            if ($order->order_type !== \App\Enums\OrderType::DELIVERY) {
                return response(['status' => false, 'message' => 'This order does not require delivery.'], 422);
            }
            if ($order->delivery_boy_id > 0) {
                return response(['status' => false, 'message' => 'This order is already assigned to another driver.'], 422);
            }
            if ($order->status !== \App\Enums\OrderStatus::ACCEPT) {
                return response(['status' => false, 'message' => 'This order is not confirmed yet.'], 422);
            }

            $order->delivery_boy_id = \Illuminate\Support\Facades\Auth::id();
            $order->save();

            \App\Events\SendOrderDeliveryBoyMail::dispatch(['order_id' => $order->id, 'status' => 101]);
            \App\Events\SendOrderDeliveryBoySms::dispatch(['order_id' => $order->id, 'status' => 101]);
            \App\Events\SendOrderDeliveryBoyPush::dispatch(['order_id' => $order->id, 'status' => 101]);

            return new OrderDetailsResource($order);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function orderCount()
    {
        try {
            return new DeliveryBoyOrderCountResource($this->orderService->deliveryBoyOrderCount());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function deliveryBoyOrderChangeStatus(Order $order, OrderStatusRequest $request)
    {
        $this->authorize('deliver', $order);
        try {
            return new OrderDetailsResource($this->orderService->deliveryBoyOrderChangeStatus($order, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
