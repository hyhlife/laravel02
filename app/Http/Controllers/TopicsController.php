<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\User;
use App\Models\Link;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Handlers\ImageUploadHandler;


class TopicsController extends Controller
{
	// 'except' => ['index', 'show'] —— 对除了 index() 和 show() 以外的方法使用 auth 中间件进行认证。
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request, Topic $topic, User $user, Link $link)
    {   
        $categories = Category::orderBy('order','asc')->get();
        $topics = $topic->withOrder($request->order)
                        ->with('user', 'category')  // 预加载防止 N+1 问题
                        ->paginate(20);
        $active_users = $user->getActiveUsers();
        $links = $link->getAllCached();
        return view('topics.index', compact('topics','active_users','links','categories'));
    }

    public function show(Request $request, Topic $topic)
    {
        $categories = Category::orderBy('order','asc')->get();
    	// URL 矫正
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }
        return view('topics.show', compact('topic','categories'));
    }

	public function create(Topic $topic)
	{
        $categories = Category::orderBy('order','asc')->get();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request, Topic $topic)
	{
		$topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();
        return redirect()->to($topic->link())->with('success', '帖子创建成功！');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::orderBy('order','asc')->get();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
        $update = $request->all();
        $update['slug'] = '';
		$topic->update($update);

		return redirect()->to($topic->link())->with('message', '帖子修改成功!');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', '帖子删除成功!');
	}

	public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = Storage::disk('minio')->put('/huangyanhong/topic/'.Auth::user()->id, $file);
            if ($result) {
                $data['file_path'] = env('MINIO_URL').'/'.$result;
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
            // $result = $uploader->save($file, 'topics', \Auth::id(), 1024);
            // // 图片保存成功的话
            // if ($result) {
            //     $data['file_path'] = $result['path'];
            //     $data['msg']       = "上传成功!";
            //     $data['success']   = true;
            // }
        }
        return $data;
    }

}