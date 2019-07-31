<?php

namespace App\Http\Controllers;

use App\Exceptions\ValidatorException;
use App\Repositories\Traits\EloquentTransactional;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends BaseController
{
    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests,
        EloquentTransactional;

    /**
     * Return a new JSON response from the application.
     *
     * @param mixed        $message Message
     * @param string|array $data    Data
     * @param int          $status  Status code
     * @param array        $headers Headers
     *
     * @return JsonResponse;
     */
    public function responseSuccess($message, $data = [], $status = Response::HTTP_OK, array $headers = [])
    {
        return response()->json([
            'status_code' => $status,
            'message' => $message,
            'data' => $data,
        ], $status, $headers);
    }

    /**
     * Return a new JSON response when error forbidden exception.
     *
     * @param mixed $message Message
     * @param array $headers Headers
     *
     * @return JsonResponse;
     */
    public function responseForbidden($message, array $headers = [])
    {
        if ($message instanceof Exception) {
            $message = $message->getMessage() ?? '';
        }

        return $this->responseError(
            $message,
            [],
            Response::HTTP_FORBIDDEN,
            $headers
        );
    }

    /**
     * Return a new JSON response when error validation exception.
     *
     * @param mixed $error   Error response
     * @param array $headers Headers
     *
     * @return JsonResponse;
     */
    public function responseValidation($error, array $headers = [])
    {
        if ($error instanceof ValidatorException) {
            $error = $error->toArray();
        }

        if (!is_array($error)) {
            $error = [];
        }

        return $this->responseError(
            trans('messages.validate.fail'),
            $error,
            Response::HTTP_UNPROCESSABLE_ENTITY,
            $headers
        );
    }

    /**
     * Return a new JSON response when error not found exception.
     *
     * @param mixed $error   Error response
     * @param array $headers Headers
     *
     * @return JsonResponse;
     */
    public function responseNotFound($error, array $headers = [])
    {
        if ($error instanceof ModelNotFoundException) {
            $message = trans('messages.' . $error->getModel() . '.not_found');
        } else {
            $message = $error->getMessage() ?? '';
        }

        return $this->responseError(
            $message,
            [],
            Response::HTTP_NOT_FOUND,
            $headers
        );
    }

    /**
     * Return a new JSON response from the application.
     *
     * @param mixed        $message Message
     * @param string|array $error   Error response
     * @param int          $status  Status code
     * @param array        $headers Headers
     *
     * @return JsonResponse;
     */
    public function responseError(
        $message,
        $error = [],
        $status = Response::HTTP_INTERNAL_SERVER_ERROR,
        array $headers = []
    ) {
        return response()->json([
            'status_code' => $status,
            'message' => $message,
            'errors' => $error,
        ], $status, $headers);
    }
}
