<?php

namespace App\Services;

use App\Enums\GatewayMode;
use Exception;
use App\Models\GatewayOption;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Log;
use Dipokhalder\EnvEditor\EnvEditor;
use App\Http\Requests\PaginateRequest;
use Illuminate\Support\Facades\Artisan;


class PaymentGatewayService
{
    public EnvEditor $envService;

    public function __construct(EnvEditor $envEditor)
    {
        $this->envService = $envEditor;
    }


    public object $gateway;
    protected array $paymentGatewayFilter = [
        'name',
        'slug',
        'status'
    ];

    protected array $exceptFilter = [
        'excepts'
    ];

    /**
     * @throws Exception
     */
    public function list(PaginateRequest $request)
    {
        try {
            $requests    = $request->all();
            $method      = $request->get('paginate', 0) == 1 ? 'paginate' : 'get';
            $methodValue = $request->get('paginate', 0) == 1 ? $request->get('per_page', 10) : '*';
            $orderColumn = $request->get('order_column') ?? 'id';
            $orderType   = $request->get('order_type') ?? 'asc';

            $query = PaymentGateway::with('gatewayOptions')->where(function ($query) use ($requests) {
                foreach ($requests as $key => $request) {
                    if (in_array($key, $this->paymentGatewayFilter)) {
                        $query->where($key, 'like', '%' . $request . '%');
                    }

                    if (in_array($key, $this->exceptFilter)) {
                        $explodes = explode('|', $request);
                        if (is_array($explodes)) {
                            foreach ($explodes as $explode) {
                                $query->where('id', '!=', $explode);
                            }
                        }
                    }
                }
            })->orderBy($orderColumn, $orderType);

            if ($method === 'paginate') {
                return $query->paginate($methodValue);
            }

            $gateways = $query->get();
            $paystack = $gateways->firstWhere('slug', 'paystack');
            if ($paystack) {
                $gateways = $gateways->reject(function ($g) {
                    return $g->slug === 'paystack';
                })->values();
                $gateways->splice(1, 0, [$paystack]);
            }

            return $gateways;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    /**
     * @throws Exception
     */
    public function update($validationRequests): object
    {
        try {
            $paymentType = $validationRequests['payment_type'] ?? null;
            $this->gateway = $paymentType ? PaymentGateway::where('slug', $paymentType)->first() : null;

            if (!blank($validationRequests)) {
                foreach ($validationRequests as $key => $value) {
                    if ($key === 'payment_type') {
                        continue;
                    }

                    $option = GatewayOption::where('option', $key)->first();
                    if (!blank($option)) {
                        $option->value = $value;
                        $option->save();
                    }

                    if (str_contains($key, 'status')) {
                        if (!blank($option)) {
                            $this->gateway = PaymentGateway::find($option->model_id);
                        }
                        if (!blank($this->gateway)) {
                            $this->gateway->status = $value;
                            $this->gateway->save();
                        }
                    }
                }
            }

            if (!blank($this->gateway)) {
                $this->gateway->load('gatewayOptions');
            } else if ($paymentType) {
                $this->gateway = PaymentGateway::with('gatewayOptions')->where('slug', $paymentType)->first();
            }
            return $this->gateway;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
