<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Models\Category;
use App\Handlers\ImageUploadHandler;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth', ['except' => ['show']]);
    }
    //
    public function show(User $user)
    {
        $categories = Category::orderBy('order','asc')->get();
        return view('users.show', compact('user','categories'));
    }

    public function edit(User $user){
    	$this->authorize('update', $user);
        $categories = Category::orderBy('order','asc')->get();
    	return view('users.edit', compact('user','categories'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader,  User $user)
    {
    	$this->authorize('update', $user);
    	$data = $request->all();
        if ($request->avatar) {
            $result = Storage::disk('minio')->put('/huangyanhong/avatar', $request->file('avatar'));
            if ($result) {
                $data['avatar'] = $result;
            }
            // $result = $uploader->save($request->avatar, 'avatars', $user->id);
            // if ($result) {
            //     $data['avatar'] = $result['path'];
            // }
        }

        $user->update($data);

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
