<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Intervention\Image\Facades\Image;

class GalleryController extends Controller
{
    public function index(Request $request){
        $year = Carbon::now()->year;
        $showSome = [];
        if($request->return){
            $year = Carbon::now()->year;
            foreach ($request->return as $image) {
                $gallery = Gallery::where('id', $image)->first();
                if ($gallery->type == "jpg" or $gallery->type == "png" or $gallery->type == "jpeg" or $gallery->type == "svg" or $gallery->type == "tif" or $gallery->type == "gif" or $gallery->type == "jfif"){
                    $folder = $_SERVER['DOCUMENT_ROOT'] .'/upload/image/' . $year . '/';
                    if (!file_exists($folder)){
                        mkdir($folder , 0755 , true);
                    }
                    File::move($gallery->path , $folder . $gallery->name);
                    $gallery->update([
                        'status' => '0',
                        'path' => $folder . $gallery->name,
                        'url' => '/upload/image/' . $year . '/' . $gallery->name,
                    ]);
                }
                elseif ($gallery->type == "rar" or $gallery->type == "zip"){
                    $folder = $_SERVER['DOCUMENT_ROOT'] .'/upload/file/' . $year . '/';
                    if (!file_exists($folder)){
                        mkdir($folder , 0755 , true);
                    }
                    File::move($gallery->path , $folder . $gallery->name);
                    $gallery->update([
                        'status' => '0',
                        'path' => $folder . $gallery->name,
                        'url' => '/upload/file/' . $year . '/' . $gallery->name,
                    ]);
                }
                elseif ($gallery->type == "mp4" or $gallery->type == "mkv"){
                    $folder = $_SERVER['DOCUMENT_ROOT'] .'/upload/movie/' . $year . '/';
                    if (!file_exists($folder)){
                        mkdir($folder , 0755 , true);
                    }
                    File::move($gallery->path , $folder . $gallery->name);
                    $gallery->update([
                        'status' => '0',
                        'path' => $folder . $gallery->name,
                        'url' => '/upload/movie/' . $year . '/' . $gallery->name,
                    ]);
                }
                elseif ($gallery->type == "mp3"){
                    $folder = $_SERVER['DOCUMENT_ROOT'] .'/upload/music/' . $year . '/';
                    if (!file_exists($folder)){
                        mkdir($folder , 0755 , true);
                    }
                    File::move($gallery->path , $folder . $gallery->name);
                    $gallery->update([
                        'status' => '0',
                        'path' => $folder . $gallery->name,
                        'url' => '/upload/music/' . $year . '/' . $gallery->name,
                    ]);
                }
            }
        }

        if($request->name){
            if(!$request->width || !$request->height){
                if($request->name){
                    $file = Gallery::where('id' , $request->fileId)->first();
                    if ($file->type == "jpg" or $file->type == "png" or $file->type == "jpeg" or $file->type == "svg" or $file->type == "tif" or $file->type == "gif" or $file->type == "jfif"){
                        $url = "/upload/image/" . $year;
                    }
                    elseif ($file->type == "rar" or $file->type == "zip"){
                        $url = "/upload/file/" . $year;
                    }
                    elseif ($file->type == "mp3"){
                        $url = "/upload/music/" . $year;
                    }
                    elseif ($file->type == "mp4" or $file->type == "mkv"){
                        $url = "/upload/movie/" . $year;
                    }
                    File::move($file->path , $_SERVER['DOCUMENT_ROOT'] .$url . '/' . $request->name);
                    $file->update([
                        'name' => $request->name,
                        'url' => $url . '/' . $request->name ,
                        'path' => $_SERVER['DOCUMENT_ROOT'] .$url . '/' . $request->name,
                    ]);
                }
            }
            else{
                $gallery = Gallery::where('id' , $request->fileId)->first();
                if($request->name){
                    if ($gallery->type == "jpg" or $gallery->type == "png" or $gallery->type == "jpeg" or $gallery->type == "svg" or $gallery->type == "tif" or $gallery->type == "gif" or $gallery->type == "jfif"){
                        $url = "/upload/image/" . $year;
                    }
                    elseif ($gallery->type == "rar" or $gallery->type == "zip"){
                        $url = "/upload/file/" . $year;
                    }
                    elseif ($gallery->type == "mp3"){
                        $url = "/upload/music/" . $year;
                    }
                    elseif ($gallery->type == "mp4" or $gallery->type == "mkv"){
                        $url = "/upload/movie/" . $year;
                    }
                    File::move($gallery->path , $_SERVER['DOCUMENT_ROOT'] .$url . '/' . $request->name);
                    $gallery->update([
                        'name' => $request->name,
                        'url' => $url . '/' . $request->name ,
                        'path' => $_SERVER['DOCUMENT_ROOT'] .$url . '/' . $request->name,
                    ]);
                }
                if($request->quality == null){
                    $request->quality = '70';
                }
                $year = Carbon::now()->year;
                $path = $_SERVER['DOCUMENT_ROOT'] .'/upload/image/' . $year . '/';
                $url = '/upload/image/' . $year . '/';
                $name = explode('.' , $gallery->name);
                $img = Image::make($gallery->path)->resize($request->width , $request->height)->save($_SERVER['DOCUMENT_ROOT'] .'/upload/image/' . $year . '/'. $name[0] . '@' . $request->width . 'x' . $request->height . '.' . $gallery->type ,  $request->quality , $gallery->type);
                $sizefile = $img->filesize()/1000;
                if( $sizefile > 1000){
                    $size=round($sizefile/1000 ,2) . 'mb';
                }else{
                    $size=round($sizefile) . 'kb';
                }
                Gallery::create([
                    'name' => $name[0] . '@' . $request->width . 'x' . $request->height . '.' . $gallery->type,
                    'size' => $size,
                    'type' => $gallery->type,
                    'user_id' => auth()->user()->id,
                    'url' => $url . $name[0] . '@' . $request->width . 'x' . $request->height . '.' . $gallery->type,
                    'path' => $path . $name[0] . '@' . $request->width . 'x' . $request->height . '.' . $gallery->type,
                ]);
            }
        }
        if ($request->crop){
            $image = Gallery::latest()->where('id' , $request->crop)->first();
            $imageCrop = $image->url;
        }else{
            $imageCrop = '';
        }
        if ($request->container == 0){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->where('status' , 0)->latest()->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->where('status' , 0)->latest()->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->where('status' , '!=' , 2)->latest()->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->where("name" , "LIKE" , "%{$request->search}%")->where('status' , '!=' , 2)->latest()->paginate(40);
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->whereDate('created_at',$request->date)->where('status' , '!=' , 2)->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->latest()->whereDate('created_at',$request->date)->where('status' , '!=' , 2)->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->where('status' , '!=' , 2)->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->latest()->where('status' , '!=' , 2)->paginate(40);
                    }
                }
            }
        }
        if ($request->container == 1){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->latest()->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->where('status' , '!=' , 2)->paginate(40);
                    }else{
                        $galleries = Gallery::where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->latest()->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->where('status' , '!=' , 2)->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->latest()->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->where('status' , '!=' , 2)->paginate(40);
                    }else{
                        $galleries = Gallery::where("name" , "LIKE" , "%{$request->search}%")->latest()->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->where('status' , '!=' , 2)->paginate(40);
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->where('status' , '!=' , 2)->whereDate('created_at',$request->date)->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->where('status' , '!=' , 2)->whereDate('created_at',$request->date)->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->where('status' , '!=' , 2)->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->where('status' , '!=' , 2)->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->paginate(40);
                    }
                }
            }
        }
        if ($request->container == 2){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->latest()->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->paginate(40);
                    }else{
                        $galleries = Gallery::where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->latest()->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->latest()->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->paginate(40);
                    }else{
                        $galleries = Gallery::where("name" , "LIKE" , "%{$request->search}%")->latest()->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->paginate(40);
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->atest()->whereDate('created_at',$request->date)->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->whereDate('created_at',$request->date)->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->paginate(40);
                    }
                }
            }
        }
        if ($request->container == 3){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->latest()->where('status' , '!=' , 2)->whereIn('type', ['mp4','mkv'])->paginate(40);
                    }else{
                        $galleries = Gallery::where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->latest()->where('status' , '!=' , 2)->whereIn('type', ['mp4','mkv'])->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->latest()->where('status' , '!=' , 2)->whereIn('type', ['mp4','mkv'])->paginate(40);
                    }else{
                        $galleries = Gallery::where("name" , "LIKE" , "%{$request->search}%")->latest()->where('status' , '!=' , 2)->whereIn('type', ['mp4','mkv'])->paginate(40);
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->where('status' , '!=' , 2)->whereDate('created_at',$request->date)->whereIn('type', ['mp4','mkv'])->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->where('status' , '!=' , 2)->whereDate('created_at',$request->date)->whereIn('type', ['mp4','mkv'])->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->where('status' , '!=' , 2)->whereIn('type', ['mp4','mkv'])->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->where('status' , '!=' , 2)->whereIn('type', ['mp4','mkv'])->paginate(40);
                    }
                }
            }
        }
        if ($request->container == 4){
            if ($request->search){
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->latest()->where('status' , 2)->paginate(40);
                    }else{
                        $galleries = Gallery::where("name" , "LIKE" , "%{$request->search}%")->whereDate('created_at',$request->date)->latest()->where('status' , 2)->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->where("name" , "LIKE" , "%{$request->search}%")->latest()->where('status' , 2)->paginate(40);
                    }else{
                        $galleries = Gallery::where("name" , "LIKE" , "%{$request->search}%")->latest()->where('status' , 2)->paginate(40);
                    }
                }
            }else{
                if ($request->date){
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->whereDate('created_at',$request->date)->where('status' , 2)->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->whereDate('created_at',$request->date)->where('status' , 2)->paginate(40);
                    }
                }else{
                    if (count($showSome) >= 1){
                        $galleries = auth()->user()->gallery()->latest()->where('status' , 2)->paginate(40);
                    }else{
                        $galleries = Gallery::latest()->where('status' , 2)->paginate(40);
                    }
                }
            }
        }
        $counts = [];
        $count1 = Gallery::latest()->where('status' , '!=' , 2)->count();
        $count2 = Gallery::latest()->where('status' , '!=' , 2)->whereIn('type', ['gif','jpeg','jpg','png','svg','tif','jfif'])->count();
        $count3 = Gallery::latest()->where('status' , '!=' , 2)->whereIn('type', ['rar','zip'])->count();
        $count4 = Gallery::latest()->where('status' , '!=' , 2)->whereIn('type', ['mp4','mkv'])->count();
        $count5 = Gallery::latest()->where('status' , 2)->count();
        array_push($counts , $count1);
        array_push($counts , $count2);
        array_push($counts , $count3);
        array_push($counts , $count4);
        array_push($counts , $count5);
        Inertia::setRootView('admin');
        return Inertia::render('GalleryPanel' , [
            'galleries' => $galleries,
            'imageCrop' => $imageCrop,
            'counts' => $counts,
        ]);
    }

    public function create(Request $request){
        $year = Carbon::now()->year;
        $folder = public_path('upload/image/' . $year . '/');
        if (!file_exists($folder)){
            mkdir($folder , 0755 , true);
        }
        $file = $request->file;
        $name = $file->getClientOriginalName();
        $type = $file->getClientOriginalExtension();
        $sizefile = $file->getsize()/1000;
        if( $sizefile > 1000){
            $size=round($sizefile/1000 ,2) . 'mb';
        }else{
            $size=round($sizefile) . 'kb';
        }
        if ($type == "jpg" or $type == "JPG" or $type == "png" or $type == "jpeg" or $type == "svg" or $type == "tif" or $type == "gif" or $type == "jfif"){
            $url = "/upload/image/" . $year;
            $path = $file->move($_SERVER['DOCUMENT_ROOT'] .$url , $name);
            Gallery::create([
                'name' => $name,
                'size' => $size,
                'type' => $type,
                'user_id' => auth()->user()->id,
                'url' => $url . '/' . $name ,
                'path' => $path->getRealPath(),
            ]);
        }
        elseif ($type == "rar" or $type == "zip"){
            $url = "/upload/file/" . $year;
            $path = $file->move(storage_path($url) , $name);
            Gallery::create([
                'name' => $name,
                'size' => $size,
                'type' => $type,
                'user_id' => auth()->user()->id,
                'url' => $url . '/' . $name ,
                'path' => $path->getRealPath(),
            ]);
        }
        elseif ($type == "mp3"){
            $url = "/upload/music/" . $year;
            $path = $file->move($_SERVER['DOCUMENT_ROOT'] .$url , $name);
            Gallery::create([
                'name' => $name,
                'size' => $size,
                'type' => $type,
                'user_id' => auth()->user()->id,
                'url' => $url . '/' . $name ,
                'path' => $path->getRealPath(),
            ]);
        }
        elseif ($type == "mp4" or $type == "mkv"){
            $url = "/upload/movie/" . $year;
            $path = $file->move($_SERVER['DOCUMENT_ROOT'] .$url , $name);
            Gallery::create([
                'name' => $name,
                'size' => $size,
                'user_id' => auth()->user()->id,
                'type' => $type,
                'url' => $url . '/' . $name ,
                'path' => $path->getRealPath(),
            ]);
        }
        return redirect('/admin/gallery');
    }

    public function createImage(Request $request){
        $year = Carbon::now()->year;
        $folder = public_path('upload/image/' . $year . '/');
        if (!file_exists($folder)){
            mkdir($folder , 0755 , true);
        }
        $file = $request->file('image');
        $name = $file->getClientOriginalName();
        $type = $file->getClientOriginalExtension();
        $sizefile = $file->getsize()/1000;
        if( $sizefile > 1000){
            $size=round($sizefile/1000 ,2) . 'mb';
        }else{
            $size=round($sizefile) . 'kb';
        }
        if ($type == "jpg" or $type == "JPG" or $type == "png" or $type == "jpeg" or $type == "svg" or $type == "tif" or $type == "gif" or $type == "jfif"){
            $url = "/upload/image/" . $year;
            $path = $file->move($_SERVER['DOCUMENT_ROOT'] .$url , $name);
            $img = Gallery::create([
                'name' => $name,
                'size' => $size,
                'type' => $type,
                'user_id' => auth()->user()->id,
                'url' => $url . '/' . $name ,
                'path' => $path->getRealPath(),
            ]);
            return $img;
        }
        elseif ($type == "rar" or $type == "zip"){
            $url = "/upload/file/" . $year;
            $path = $file->move(storage_path($url) , $name);
            $img = Gallery::create([
                'name' => $name,
                'size' => $size,
                'type' => $type,
                'user_id' => auth()->user()->id,
                'url' => $url . '/' . $name ,
                'path' => $path->getRealPath(),
            ]);
            return $img;
        }
    }

    public function show(Request $request){
        return Gallery::where('id', $request->show)->first();
    }

    public function cropImage(Request $request){
        $year = Carbon::now()->year;
        $file = Gallery::where('id', $request->name)->first();
        $name = explode('.' , $file->name);
        $data = $request->input('img');
        list($type , $data) = explode(';' , $data);
        list(, $data) = explode(',' , $data);
        $data = base64_decode($data);
        $imageName = $name[0] . '-Crop-' . time() . '.jpg';
        $url = '/upload/image/' . $year . '/';
        $path = $_SERVER['DOCUMENT_ROOT'] .'/upload/image/' . $year . '/';
        if (!file_exists($path)){
            mkdir($path , 0755 , true);
        }
        file_put_contents($path . $imageName , $data);

        $img = Image::make($path . $imageName)->save($_SERVER['DOCUMENT_ROOT'] .'/upload/image/' . $year . '/' . $imageName ,  '70' , 'jpg');
        $sizefile = $img->filesize()/1000;
        if( $sizefile > 1000){
            $size=round($sizefile/1000 ,2) . 'mb';
        }else{
            $size=round($sizefile) . 'kb';
        }
        Gallery::create([
            'name' => $imageName,
            'size' => $size,
            'type' => 'jpg',
            'user_id' => auth()->user()->id,
            'url' => $url . $imageName,
            'path' => $path . $imageName,
        ]);
        return redirect('/admin/gallery');
    }

    public function getImage(){
        return Gallery::latest()->where('status', '!=' , 2)->get();
    }

    public function destroy(Request $request){
        foreach ($request->value as $image) {
            $gallery = Gallery::where('id', $image)->first();
            if ($gallery->status == 0){
                $year = Carbon::now()->year;
                $folder = $_SERVER['DOCUMENT_ROOT'] .'/upload/trash/' . $year . '/';
                if (!file_exists($folder)){
                    mkdir($folder , 0755 , true);
                }
                if (file_exists($gallery->path)){
                    File::move($gallery->path , $folder . $gallery->name);
                    $gallery->update([
                        'status' => '2',
                        'path' => $folder . $gallery->name,
                        'url' => '/upload/trash/' . $year . '/' . $gallery->name,
                    ]);
                }else{
                    $gallery->update([
                        'status' => '2',
                        'path' => $folder . $gallery->name,
                        'url' => '/upload/trash/' . $year . '/' . $gallery->name,
                    ]);
                }
            }else{
                $gallery->delete();
                if (file_exists($gallery->path)){
                    File::delete($gallery->path);
                }
            }
        }
        return redirect('/admin/gallery');
    }
}
