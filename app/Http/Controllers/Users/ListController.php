<?php

namespace App\Http\Controllers\Users;

use App\Services\Users\ListService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ListController extends Controller
{
    /**
     * List service
     *
     * @var $listService
     */
    protected $listService;

    /**
     * Constructor.
     *
     * @param ListService $listService List service
     *
     * @return void
     */
    public function __construct(
        ListService $listService
    ) {
        $this->listService = $listService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request Request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $searchData = $request->only([
            'team_id',
            'code',
            'name',
            'address',
            'email',
            'status'
        ]);

        $sortData = [
            'sort_column' => $request->get('sort_column', 'name'),
            'sort_direction' => $request->get('sort_direction', 'asc'),
            'per_page' => intval($request->get('per_page', 10))
        ];

        try {
            $users = $this->listService->handle($searchData, $sortData);
        } catch (Exception $ex) {
            return $this->responseError(
                trans('messages.error.list', ['item' => trans('validation.attributes.user')])
            );
        }

        return $this->responseSuccess(
            trans('messages.success.list', ['item' => trans('validation.attributes.user')]), $users
        );
    }
}
