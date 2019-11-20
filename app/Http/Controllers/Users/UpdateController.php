<?php

namespace App\Http\Controllers\Users;

use App\Services\Users\UpdateService;
use App\Validators\Exceptions\ValidatorException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class UpdateController extends Controller
{

    /**
     * Update service
     *
     * @var $updateService
     */
    protected $updateService;

    /**
     * Constructor.
     *
     * @param UpdateService $updateService Update service
     *
     * @return void
     */
    public function __construct(
        UpdateService $updateService
    ) {
        $this->updateService = $updateService;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request Request
     * @param int     $id      Id user
     *
     * @return Response
     */
    public function __invoke(Request $request, $id)
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
            'status'
        ]);

        try {
            $user = $this->updateService->handle($id, $input);
        } catch (ValidatorException $e) {
            return $this->responseError(
                trans('messages.validate.fail'),
                $e->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        } catch (ModelNotFoundException $ex) {
            return $this->responseError($ex);
        } catch (Exception $ex) {
            return $this->responseError(
                trans('messages.error.update', ['item' => trans('validation.attributes.user')]),
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->responseSuccess(
            trans('messages.success.update', ['item' => trans('validation.attributes.user')]),
            ['id' => $user->id],
            Response::HTTP_CREATED
        );
    }
}
