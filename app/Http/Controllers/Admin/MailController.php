<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MailRequest;
use App\Http\Requests\MailTestRequest;
use App\Http\Resources\MailResource;
use App\Services\MailService;
use Exception;
use Illuminate\Http\JsonResponse;

class MailController extends AdminController
{
    private MailService $mailService;

    public function __construct(MailService $mailService)
    {
        parent::__construct();
        $this->mailService = $mailService;
        $this->middleware(['permission:settings'])->only(['update', 'testMail', 'sampleData']);
    }

    public function index() : \Illuminate\Http\Response | MailResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new MailResource($this->mailService->list());
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(MailRequest $request) : \Illuminate\Http\Response | MailResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new MailResource($this->mailService->update($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function sampleData() : JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'data'   => $this->mailService->sampleData(),
            ]);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function testMail(MailTestRequest $request) : JsonResponse
    {
        try {
            $result = $this->mailService->testMail($request);
            return response()->json($result);
        } catch (Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
