<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 */
class LogoutController extends ApiController
{

    /**
     * Handle the incoming request.
     *
     * @param Request $request Request
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request)
    {
        $this->guard()->logout();

        return $this->responseSuccess(trans('auth.logout'));
    }

    /**
     * Auth guard
     *
     * @return mixed
     */
    private function guard()
    {
        return Auth::guard();
    }
}
