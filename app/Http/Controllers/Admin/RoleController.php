<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Pay;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request){
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش مقام')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف مقام')->pluck('name');
        $checkAdds =  auth()->user()->getAllPermissions()->where('name' , 'افزودن مقام')->pluck('name');
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
        if($request->value){
            foreach ($request->value as $value) {
                $tax = Role::where('id', $value)->first();
                foreach ($tax->permissions as $permission) {
                    $tax->revokePermissionTo($permission);
                }
            }
            DB::table('roles')->whereIn('id', $request->value)->delete();
        }
        if($request->name){
            $request->validate([
                'name' => 'required|max:255',
            ]);
            if($request->roleId){
                $role = Role::where('id' , $request->roleId)->first();
                $role->update([
                    'name'=> $request->name,
                    'updated_at'=> Carbon::now(),
                ]);
                foreach ($role->permissions as $permission) {
                    $role->revokePermissionTo($permission);
                }
                foreach ($request->permission as $permission) {
                    $role->givePermissionTo($permission);
                }
            }else{
                $role = Role::where('name' , $request->name)->first();
                if (!$role){
                    $role = Role::create([
                        'name'=> $request->name,
                    ]);
                    foreach ($request->permission as $permission) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }
        if($request->roleId && !$request->name){
            $roleEdit = Role::where('id' , $request->roleId)->with('permissions')->first();
        }else{
            $roleEdit = '';
        }
        if ($request->search){
            $search = Role::where("name" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            if(count($search) == 0){
                $search = Role::where("id" , "LIKE" , "%{$request->search}%")->pluck('id')->toArray();
            }
        }else{
            $search = Role::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Role::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Role::latest()->pluck('id')->toArray();
        }
        $arrayFilter = array_intersect($search,$date);
        $roles = Role::latest()->whereIn('id' , $arrayFilter)->paginate(30);

        $permissions = Permission::latest()->get();
        $labels = ['#','آیدی','نام','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('RolePanel' , [
            'labels' => $labels,
            'adds' => $adds,
            'edits' => $edits,
            'deletes' => $deletes,
            'roleEdit' => $roleEdit,
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }
    public function user(Request $request){
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
                $user = User::where('id' , $value)->first();
                $user->removeRole($user->roles->first());
            }
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
        $noRole = User::doesntHave('roles')->pluck('id');
        $allUser = User::latest()->whereIn('id' , $arrayFilter)->whereNotIn('id',$noRole)->with('roles')->paginate(30);
        $roles = Role::latest()->get();
        Inertia::setRootView('admin');
        return Inertia::render('UserRole', [
            'allUser' => $allUser,
            'roles' => $roles,
        ]);
    }
}
