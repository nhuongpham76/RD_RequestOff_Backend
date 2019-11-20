<?php

namespace App\Http\Controllers\Users;

use App\Services\Users\CreateService;
use App\Validators\Exceptions\ValidatorException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class CreateController extends Controller
{

    /**
     * Create service
     *
     * @var $createService
     */
    protected $createService;

    /**
     * Constructor.
     *
     * @param CreateService $createService Create service
     *
     * @return void
     */
    public function __construct(
        CreateService $createService
    ) {
        $this->createService = $createService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request Request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $input = $request->only([
            'team_id',
            'code',
            'name',
            'email',
            'password',
            'password_confirmation',
            'phone',
            'address',
            'role_id',
        ]);

        try {
            $user = $this->createService->handle($input);
        } catch (ValidatorException $e) {
            return $this->responseError(
                trans('messages.validate.fail'),
                $e->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (Exception $ex) {
            return $this->responseError(
                trans('messages.error.create', ['item' => trans('validation.attributes.user')]),
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->responseSuccess(
            trans('messages.success.create', ['item' => trans('validation.attributes.user')]),
            ['id' => $user->id],
            Response::HTTP_CREATED
        );
    }
}
