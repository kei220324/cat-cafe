<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;

class AdminBlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs=Blog::all();
        return view('admin.blogs.index',['blogs'=>$blogs]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
          return view('admin.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request)
    {
        $saveImagePath=$request->file('image')->store('blogs','public');
       $blog = new Blog($request->validated());
       $blog->image=$saveImagePath;
        $blog->save();

        return to_route('admin.blogs.index')->with('success','ブログを投稿しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

//    指定したブログの編集画面 
    public function edit(string $id)
    {
      $blog=Blog::findOrfail($id);
       return view('admin.blogs.edit',['blog'=>$blog]);
  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, string $id)
    {
       $blog= Blog::findOrfail($id);
      
      
       $updateData=$request->validated();
       dd($updateData);

        //画像を変更する場合
       if($request->has('image')){
        //変更前の画像を削除
        Storage::disk('public')->delete($blog->image);
        //変更後の画像をアップロード、保存パスをデータにセット
        $updateData['iamge']=$reqeust->file('image')->store('blogs','public');
       }
       $blog->update($updateData);
       
    return to_route('admin.blogs.index')->with('success','ブログを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
