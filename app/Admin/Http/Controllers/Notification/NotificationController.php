<?php

namespace App\Admin\Http\Controllers\Notification;

use Illuminate\Http\Request;
use App\Admin\Http\Controllers\Controller;
use App\Enums\Notification\NotificationStatus;
use App\Admin\DataTables\Notification\NotificationDataTable;
use App\Admin\Http\Requests\Notification\NotificationRequest;
use App\Admin\Services\Notification\NotificationServiceInterface;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;

class NotificationController extends Controller
{


    public function __construct(
        NotificationServiceInterface $service,
        NotificationRepositoryInterface $repository
    ) {
        parent::__construct();
        $this->service = $service;
        $this->repository = $repository;
    }

    public function getView()
    {
        return [
            'index' => 'admin.notification.index',
            'create' => 'admin.notification.create',
            'edit' => 'admin.notification.edit'

        ];
    }


    public function getRoute()
    {
        return [
            'index' => 'admin.notify.index',
            'create' => 'admin.notify.create',
            'edit' => 'admin.notify.edit',
        ];
    }


    public function index(NotificationDataTable $dataTable)
    {

        $actionMultiple = [
            'delete' => 'Xoá',
            'publishedStatus' => 'Đã xuất bản',
            'draftStatus' => 'Bản nháp'
        ];
        return $dataTable->render($this->view['index'], [
            'status' => NotificationStatus::asSelectArray(),
            'actionMultiple' => $actionMultiple
        ]);
    }

    public function create()
    {

        return view($this->view['create'], [
            'status' => NotificationStatus::asSelectArray()
        ]);
    }

    public function store(NotificationRequest $request)
    {

        $instance = $this->service->store($request);
        if ($instance) {
            return to_route($this->route['index'])->with('success', 'Đã Thêm Thông Báo Thành Công');
        }
        return back()->with('error', __('notifyFail'))->withInput();
    }

    public function delete($id)
    {
        $this->service->delete($id);
        return to_route($this->route['index'])->with('success', __('notifySuccess'));
    }


    public function edit($id)
    {

        $notify = $this->repository->find($id);
        $users = $this->repository->getUsersForNotificationById($notify->id);
        return view(
            $this->view['edit'],
            [
                'users' => $users,
                'notify' => $notify,
                'status' => NotificationStatus::asSelectArray()
            ],
        );
    }

    public function update(NotificationRequest $request)
    {
        $id = request('id');
        $respone = $this->service->update($id, $request);
        if ($respone) {
            return to_route($this->route['index'])->with('success', 'notifySuccess');
        }
        return back()->with('error', __('notifyFail'));
    }


    public function actionMultipleRecode(Request $request){
        $boolean = $this->service->actionMultipleRecode($request);
        if($boolean){
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
