<?php

namespace App\Http\Controllers\Users;

use Inertia\Inertia;
use App\Models\Auth\User;
use Illuminate\Http\Request;
use App\Models\Auth\UserSite;
use App\Http\Controllers\Controller;

class SiteWiseUserController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('ProjectComponents/Users/SiteWiseUser');

    }
    public function SiteWiseUserList(Request $request){
        $this->validate($request, [
            'site_id' => 'required|exists:sites,id',
            'type' => 'required|in:have,have-not',
        ]);
        $users = $this->getUsers($request->site_id, $request->type);
        return reply('OK', ['data'=> $users]);
    }

    private function getUsers($site_id, $type)
    {
        $site_users = UserSite::orderBy('id')->whereSiteId($site_id);
        if ($type == 'have') {
            return $site_users->get()->load('users');
        } else {
            $user_ids = $site_users->pluck('user_id')->toArray();
            $users = User::whereNotIn('id', $user_ids)->get();
            return $users;
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'site_id' => 'required|integer|exists:sites,id',
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'required|in:add,remove',
            'site_type' => 'required|in:have,have-not',
        ]);

        if ($request->type == 'remove') {
            $site_user = UserSite::whereSiteId($request->site_id)->whereUserId($request->user_id)->first()->delete();
        } else {
            $site_user = new UserSite();
            $site_user->fill($request->all());
            $site_user->save();
        }
        $site_users = $this->getUsers($request->site_id, $request->type);
        return reply('OK', compact('site_users'));
    }
}//
