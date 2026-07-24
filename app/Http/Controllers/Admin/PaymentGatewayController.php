<?php

namespace App\Http\Controllers\Admin;


use App\Http\Requests\PaginateRequest;
use App\Http\Resources\PaymentGatewayResource;
use App\Services\PaymentGatewayService;
use Exception;
use Illuminate\Http\Request;


class PaymentGatewayController extends AdminController
{
    private PaymentGatewayService $paymentGatewayService;

    public function __construct(PaymentGatewayService $paymentGatewayService)
    {
        parent::__construct();
        $this->paymentGatewayService = $paymentGatewayService;
        $this->middleware(['permission:settings'])->only('update');
    }

    public function index(
        PaginateRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return PaymentGatewayResource::collection($this->paymentGatewayService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(
        Request $request
    ): PaymentGatewayResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        $className          = 'App\\Http\\PaymentGateways\\Requests\\' . \Illuminate\Support\Str::studly($request->payment_type);
        $gateway            = new $className;
        $validationRequests = array_merge(
            $request->validate($gateway->rules()),
            ['payment_type' => $request->payment_type]
        );

        try {
            return new PaymentGatewayResource($this->paymentGatewayService->update($validationRequests));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function setPrimary(Request $request)
    {
        $request->validate([
            'primary_gateway' => ['required', 'string']
        ]);

        \Smartisan\Settings\Facades\Settings::group('payment_gateway')->set([
            'primary_payment_gateway' => $request->primary_gateway
        ]);

        return response()->json([
            'status' => true,
            'message' => ucfirst($request->primary_gateway) . ' set as Primary Payment Gateway.',
            'primary_payment_gateway' => $request->primary_gateway
        ]);
    }
}
