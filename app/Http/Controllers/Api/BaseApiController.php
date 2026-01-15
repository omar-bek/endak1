<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class BaseApiController extends Controller
{
    protected function success(mixed $data = null, string $message = 'success', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error(string $message, int $status = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * تنفيذ callback مع try-catch وإرجاع JSON response للـ API
     */
    protected function executeApiWithTryCatch(callable $callback, string $errorMessage = 'حدث خطأ أثناء العملية'): JsonResponse
    {
        try {
            return $callback();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('بيانات غير صحيحة', 422, $e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error('السجل غير موجود', 404);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('API Database Error: ' . $e->getMessage(), [
                'exception' => $e,
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'trace' => $e->getTraceAsString()
            ]);

            $response = [
                'success' => false,
                'message' => $errorMessage,
                'errors' => null,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'sql' => $e->getSql() ?? 'N/A',
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }

            return response()->json($response, 500);
        } catch (Exception $e) {
            Log::error('API Error: ' . $e->getMessage(), [
                'exception' => $e,
                'class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            // في بيئة التطوير، أضف تفاصيل الخطأ
            $response = [
                'success' => false,
                'message' => $errorMessage,
                'errors' => null,
            ];

            if (config('app.debug')) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString()),
                ];
            }

            return response()->json($response, 500);
        }
    }
}
