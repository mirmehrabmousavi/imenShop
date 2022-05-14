<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use App\Models\Pay;
use App\Models\Comment;
use App\Models\Post;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request){
        DB::table('users')->where('seen', 0)->update(['seen' => 1]);
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش کاربر')->pluck('name');
        $checkShows =  auth()->user()->getAllPermissions()->where('name' , 'نمایش کاربر')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف کاربر')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن کاربر')->pluck('name');
        if(auth()->user()->admin == 1 or count($checkEdits) >= 1){
            $edits = 1;
        }else{
            $edits = 0;
        }
        if(auth()->user()->admin == 1 or count($checkDeletes) >= 1){
            $deletes = 1;
        }else{
            $deletes = 0;
        }
        if(auth()->user()->admin == 1 or count($checkAdds) >= 1){
            $adds = 1;
        }else{
            $adds = 0;
        }
        if(auth()->user()->admin == 1 or count($checkShows) >= 1){
            $shows = 1;
        }else{
            $shows = 0;
        }
        $users = User::get();
        foreach ($users as $user) {
            if (Cache::has('user-is-online-' . $user->id)){
                $user->update([
                    'activity' => Verta::now()->format('H:i Y-n-j')
                ]);
            }
        }

        if($request->value){
            foreach ($request->value as $value) {
                $user = User::where('id', $value)->first();
                $userF = User::where('id' , '!=', $value)->first();
                if($user) {
                    if ($user->category) $user->category()->detach();
                    $user->bookmark()->delete();
                    $user->comments()->delete();
                    $user->like()->delete();
                    $user->brand()->detach();
                    $user->payMeta()->delete();
                    $user->event()->delete();
                    $user->pay()->delete();
                    $user->cart()->delete();
                    $user->rate()->delete();
                    $user->report()->delete();
                    $user->question()->delete();
                    $user->notification()->delete();
                    $user->checkout()->delete();
                    $user->charge()->delete();
                    $user->robot()->delete();
                    $user->ticket()->delete();
                    DB::table('posts')->whereIn('id', $user->post()->pluck('id'))->update([
                        'user_id'=>$userF->id
                    ]);
                    DB::table('news')->whereIn('id', $user->news()->pluck('id'))->update([
                        'user_id'=>$userF->id
                    ]);
                    $user->document()->delete();
                    $user->guarantee()->detach();
                    $user->gallery()->delete();
                }
            }
            DB::table('users')->whereIn('id', $request->value)->delete();
        }
        if($request->show){
            $user = User::where('id' , $request->show)->first();
            $pays = Pay::latest()->where('user_id' , $user->id)->take(5)->get();

            $likePost = [];
            foreach ($user->like as $item) {
                $posts = Post::latest()->where('id' , $item->post_id)->pluck('id')->first();
                array_push($likePost , $posts);
            }
            $likePosts = Post::latest()->whereIn('id' , $likePost)->take(5)->get();

            $bookmarkPost = [];
            foreach ($user->bookmark as $item) {
                $posts = Post::latest()->where('id' , $item->post_id)->pluck('id')->first();
                array_push($bookmarkPost , $posts);
            }
            $bookmarkPosts = Post::latest()->whereIn('id' , $bookmarkPost)->take(5)->get();

            $comments = Comment::where('user_id' , $user->id)->with('post')->take(5)->get();
            $userInfo = [$user,$pays,$likePosts,$bookmarkPosts,$comments];
        }else{
            $userInfo = [];
        }
        if($request->name or $request->email or $request->number or $request->password){
            if($request->userId){
                $request->validate([
                    'name' => 'required',
                ]);
                $user = User::where('id' , $request->userId)->first();
                if ($request->password){
                    $user->update([
                        'name'=> $request->name,
                        'email'=> $request->email,
                        'number'=> $request->number,
                        'password'=> Hash::make($request->password),
                        'profile'=> $request->image,
                        'updated_at'=> Carbon::now(),
                    ]);
                    if($request->role){
                        $user->removeRole($request->role);
                        $user->syncRoles($request->role);
                    }
                    foreach ($user->permissions as $permission) {
                        $user->revokePermissionTo($permission->name);
                    }
                    foreach ($request->permission as $permission) {
                        $user->givePermissionTo($permission);
                    }
                }else{
                    $user->update([
                        'name'=> $request->name,
                        'email'=> $request->email,
                        'number'=> $request->number,
                        'profile'=> $request->image,
                        'updated_at'=> Carbon::now(),
                    ]);
                    if($request->role){
                        $user->removeRole($request->role);
                        $user->syncRoles($request->role);
                    }
                    if($user->permissions){
                        foreach ($user->permissions as $permission) {
                            $user->revokePermissionTo($permission->name);
                        }
                    }
                    if($request->permission){
                        foreach ($request->permission as $permission) {
                            $user->givePermissionTo($permission);
                        }
                    }
                }
            }else{
                $request->validate([
                    'name' => 'required|max:255',
                ]);
                $check = User::where('email' , $request->email)->first();
                if(!$check){
                    $user = User::create([
                        'name'=> $request->name,
                        'email'=> $request->email,
                        'number'=> $request->number,
                        'password'=> Hash::make($request->password),
                        'profile'=> $request->image,
                    ]);
                    if($request->role){
                        $user->removeRole($request->role);
                        $user->syncRoles($request->role);
                    }
                    foreach ($request->permission as $permission) {
                        $user->givePermissionTo($permission);
                    }
                }
            }
        }
        if($request->userId && !$request->name){
            $userEdit = User::where('id' , $request->userId)->with('roles','permissions')->first();
        }else{
            $userEdit = '';
        }


        if ($request->search){
            $search = User::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            if(count($search) == 0){
                $search = User::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            }
        }else{
            $search = User::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = User::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = User::latest()->pluck('id')->toArray();
        }
        if ($request->sortRole){
            $sortRole = User::role($request->sortRole)->pluck('id')->toArray();
        }else{
            $sortRole = User::latest()->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($search,$date,$sortRole);
        $allUser = User::latest()->whereIn('id' , $arrayFilter)->withCount('bookmark','comments','like')->with('permissions','roles')->paginate(30);
        $roles = Role::latest()->get();
        $permissions = Permission::latest()->get();
        $labels = ['#','آیدی','تصویر','نام','آخرین بازدید','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('UserPanel', [
            'labels' => $labels,
            'allUser' => $allUser,
            'adds' => $adds,
            'edits' => $edits,
            'deletes' => $deletes,
            'shows' => $shows,
            'userEdit' => $userEdit,
            'userInfo' => $userInfo,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }
}
