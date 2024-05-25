<?php

namespace App\Http\Controllers\Notification;

use App\Admin\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
class NotificationController extends Controller
{

    protected $repoNoti;
    public function __construct(NotificationRepositoryInterface $repoNoti)
    {
        parent::__construct();
        $this->repoNoti = $repoNoti;
    }


    public function getView()
    {
        return [
            'index' => 'public.notification.index',
            'detaill' =>'public.notification.detail.detail'
        ];
    }

    public function getRoute()
    {
        return [
            'index' => 'notification.index',

        ];
    }

    public function index()
    {

        if (Auth::check()) {
            $user = Auth::user();
            $notify = $user->notifications()->orderBy('created_at', 'desc')->get();
        } else {
            $notify = null;
        }

        return view($this->view['index'], ['notify' => $notify]);
    }

    public function detail($id){

        $dataNoti = $this->repoNoti->find($id);

        return view($this->view['detaill'],[
            'value' => $dataNoti,
        ]);
    }
}
