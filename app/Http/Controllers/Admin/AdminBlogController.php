<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use  App\Models\Category;
use Illuminate\Support\Facades\Auth;


class AdminBlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $user=Auth::user();
        $blogs=Blog::latest('updated_at')->paginate(10);
        return view('admin.blogs.index',['blogs'=>$blogs,'user'=>$user]);
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
    public function edit(Blog $blog)
    {
 
        $categories=Category::all();
        $cats=Cat::all();
       return view('admin.blogs.edit',['blog'=>$blog,'categories'=>$categories ,'cats'=>$cats]);
  
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, string $id)
{
   
       $blog= Blog::findOrfail($id);
      
       
       $updateData=$request->validated();
     
     
       //画像を変更する場合
       if($request->has('image')){
        //変更前の画像を削除
        Storage::disk('public')->delete($blog->image);
        //変更後の画像をアップロード、保存パスをデータにセット
$updateData['image'] = $request->file('image')->store('blogs', 'public');
       }
    
         //ブログ情報を更新
       $blog->category()->associate($updateData['category_id']);
       $blog->update($updateData);
       $blog->cats()->sync($updateData['cats'] ?? []);
       
    return to_route('admin.blogs.index')->with('success','ブログを更新しました');
    }

   //指定したIDのブログ削除処理
    public function destroy(string $id)
    {
         $blog=Blog::findOrfail($id);
     
         $blog->delete ();

         Storage::disk('public')->delete($blog->image);

 return to_route('admin.blogs.index')->with('success','ブログを削除しました');
    }
}
