<?php

namespace App\Http\Controllers\Users;

use App\Services\Users\DestroyService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Repositories\Exceptions\ForbiddenException;
use App\Http\Controllers\Controller;

class DeleteController extends Controller
{

    /**
     * Destroy service
     *
     * @var $destroyService
     */
    protected $destroyService;

    /**
     * Constructor.
     *
     * @param DestroyService $destroyService Destroy service
     *
     * @return void
     */
    public function __construct(
        DestroyService $destroyService
    ) {
        $this->destroyService = $destroyService;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id Id user
     *
     * @return Response
     */
    public function __invoke($id)
    {
        try {
            $this->destroyService->handle($id);
        } catch (ForbiddenException $e) {
            return $this->responseError($e->getMessage(), [], Response::HTTP_FORBIDDEN);
        } catch (ModelNotFoundException $ex) {
            return $this->responseError($ex);
        } catch (Exception $e) {
            return $this->responseError(
                trans('messages.error.delete', ['item' => trans('validation.attributes.user')]),
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->responseSuccess(
            trans('messages.success.delete', ['item' => trans('validation.attributes.user')])
        );
    }
}
