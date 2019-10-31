<?php

namespace App\Http\Controllers\Users;

use App\Services\Users\ShowService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Repositories\Exceptions\ForbiddenException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShowController extends Controller
{

    /**
     * Show service
     *
     * @var $showService
     */
    protected $showService;

    /**
     * Constructor.
     *
     * @param ShowService $showService Show service
     *
     * @return void
     */
    public function __construct(
        ShowService $showService
    ) {
        $this->showService = $showService;
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request Request
     * @param int     $id      Id of company
     *
     * @return Response
     */
    public function __invoke(Request $request, $id)
    {
        try {
            $data = $this->showService->handle($id);
        } catch (ForbiddenException $e) {
            return $this->responseError($e->getMessage(), [], Response::HTTP_FORBIDDEN);
        } catch (ModelNotFoundException $ex) {
            return $this->responseError($ex);
        } catch (Exception $e) {
            return $this->responseError(
                trans('messages.error.show', ['item' => trans('validation.attributes.user')]),
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->responseSuccess('', $data);
    }
}
