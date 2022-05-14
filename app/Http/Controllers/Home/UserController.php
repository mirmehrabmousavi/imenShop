<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\ActiveSms;
use App\Models\Carrier;
use App\Models\Cart;
use App\Models\Checkout;
use App\Models\Event;
use App\Models\PayMeta;
use App\Models\Rank;
use App\Models\Score;
use App\Models\Time;
use App\Models\UserMeta;
use App\Models\User;
use App\Models\Post;
use App\Models\Ticket;
use App\Models\Setting;
use App\Models\Pay;
use Hekmatinasser\Verta\Verta;
use Illuminate\Support\Facades\Hash;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function checkCode(Request $request){
        $request->validate([
            'phone' => ['required','min:17','max:17' , Rule::exists('active_sms')],
            'code' => ['required','min:6','max:6' , Rule::exists('active_sms')],
        ]);
        $check = ActiveSms::where('code',$request->code)->where('expire' , '>='  ,Carbon::now()->timestamp)->where('phone',$request->phone)->first();
        if ($check){
            $num1 = explode('-',$request->phone);
            $num2 = implode('',$num1);
            $num3 = explode(' ',$num2);
            $num4 = implode('',$num3);
            $user = User::where('id',auth()->user()->id)->first();
            Event::create([
                'type' => 10,
                'title' => 'تغییر شماره تماس',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . 'شماره تماس خود را از ' . auth()->user()->number . ' به ' . $num4 . 'تغییر داد',
            ]);
            $user->update([
                'number'=> $num4
            ]);
            $check->delete();
            return 'success';
        }else{
            return 'fail';
        }
    }
    public function checkEmail(Request $request){
        $request->validate([
            'phone' => ['required' , Rule::exists('active_sms')],
            'code' => ['required','min:6','max:6' , Rule::exists('active_sms')],
        ]);
        $check = ActiveSms::where('code',$request->code)->where('expire' , '>='  ,Carbon::now()->timestamp)->where('phone',$request->phone)->first();
        if ($check){
            $user = User::where('id',auth()->user()->id)->first();
            Event::create([
                'type' => 10,
                'title' => 'تغییر ایمیل',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . 'ایمیل خود را از ' . auth()->user()->email . ' به ' . $request->phone . 'تغییر داد',
            ]);
            $user->update([
                'email'=> $request->phone
            ]);
            $check->delete();
            return 'success';
        }else{
            return 'fail';
        }
    }
    public function userInfoCart(){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('آدرس و زمان ارسال - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if (Auth::user()){
            $cart = auth()->user()->cart()->pluck('post_id');
            if (count($cart) >= '1'){
                $count = Cart::where('user_id' , auth()->user()->id)->with('guarantee','carrier','user')->get();
                for ($i = 0; $i < count($count); $i++) {
                    $countCheck = Post::where('id', $count[$i]->post_id)->pluck('count')->first();
                    $post = Post::where('id', $count[$i]->post_id)->with('review')->first();
                    $postSize = [];
                    $postColor = [];
                    $myScore = Score::latest()->where('type' , 0)->where('user_id' , auth()->user()->id)->pluck('name')->sum();
                    $myRank = Rank::where('from' , '<=', $myScore)->where('to' , '>=', $myScore)->first();
                    if($myRank){
                        $myRankPost = $myRank->post()->where('id' , $post->id)->first();
                        if($myRankPost){
                            $finalPrice = round($myRankPost->offPrice - $myRankPost->offPrice * $myRank->off / 100);
                        }else{
                            $finalPrice = $post->price;
                        }
                    }else{
                        $finalPrice = $post->price;
                    }
                    $price = $finalPrice;
                    if ($count[$i]['color'] != '[]'){
                        $cartColor = json_decode($count[$i]['color'],true)['name'];
                        foreach (json_decode($post['review'][0]['colors'] , true) as $item) {
                            if($item['name'] == $cartColor){
                                $postColor = $item;
                                $price = $price + $postColor['price'];
                                $count[$i]->update([
                                    'color' => json_encode($postColor),
                                ]);
                                if($postColor['count'] <=0){
                                    $count[$i]->update([
                                        'color' => 'empty',
                                    ]);
                                }
                            }
                        }
                        if ($postColor == []){
                            $count[$i]->update([
                                'color' => 'empty',
                            ]);
                        }
                    }
                    if ($count[$i]['size'] != '[]'){
                        $cartSize = json_decode($count[$i]['size'],true)['name'];
                        foreach (json_decode($post['review'][0]['size'] , true) as $item) {
                            if($item['name'] == $cartSize){
                                $postSize = $item;
                                $price = $price + $postSize['price'];
                                $count[$i]->update([
                                    'size' => json_encode($postSize),
                                ]);
                                if($postSize['count'] <= 0){
                                    $count[$i]->update([
                                        'size' => 'empty',
                                    ]);
                                }
                            }
                        }
                        if ($postSize == []){
                            $count[$i]->update([
                                'size' => 'empty',
                            ]);
                        }
                    }
                    $count[$i]->update([
                        'price' => $price,
                    ]);
                    if ($countCheck - $count[$i]->count < 0 || $count[$i]['size'] == 'empty' || $count[$i]['color'] == 'empty') {
                        $count[$i]->delete();
                    }
                };
                $posts = Post::whereIn('id', $cart)->with('time')->get();
                $ids = [];
                foreach ($posts as $item){
                    if(count($item['time']) >= 1){
                        $id = $item['time'][0]['id'];
                        array_push($ids , $id);
                    }
                }
                $times = Time::whereIn('id' , $ids)->orderBy('day','DESC' )->first();
                $days = [];
                if ($times){
                    for ( $i = $times['day']; $i < $times['day']+5; $i++) {
                        $v = new Verta('+'.$i . "day");
                        $date = [
                            'dayL'=> '',
                            'to'=> $times['to'],
                            'from'=> $times['from'],
                            'day'=> $v->day,
                            'month'=> $v->format('%B'),
                            'timestamp'=> $v->timestamp,
                        ];
                        $day = Carbon::instance($v)->format('l');
                        if($day == 'Saturday'){
                            $date['dayL'] = 'چهار شنبه';
                        }
                        if($day == 'Sunday'){
                            $date['dayL'] = 'پنجشنبه';
                        }
                        if($day == 'Monday'){
                            $date['dayL'] = 'جمعه';
                        }
                        if($day == 'Tuesday'){
                            $date['dayL'] = 'شنبه';
                        }
                        if($day == 'Wednesday'){
                            $date['dayL'] = 'یکشنبه';
                        }
                        if($day == 'Thursday'){
                            $date['dayL'] = 'دوشنبه';
                        }
                        if($day == 'Friday'){
                            $date['dayL'] = 'سه شنبه';
                        }
                        array_push($days , $date);
                    }
                }
                $carriers = Carrier::latest()->get();
                if(count($count) >= 1){
                    if(!$count[0]->carrier){
                        foreach ($count as $item){
                            $item->carrier()->sync($carriers[0]['id']);
                        }
                    }
                }
                $map = Setting::where('key' , 'map')->pluck('value')->first();
                return Inertia::render('Cart/UserCart', [
                    'map' => $map,
                    'title' => $title,
                    'carriers' => $carriers,
                    'days' => $days,
                ]);
            }
            else{
                return redirect('/');
            }
        }else{
            return Inertia::render('Cart/CartIndex');
        }
    }
    public function ChangeAllUserInfo(User $user , Request $request){
        $request->validate([
            'date' => 'required|max:11',
            'name' => 'required|min:10|max:255',
            'user' => 'required|max:255',
            'post' => 'required|min:8|max:20',
            'job' => 'required|max:100',
            'code' => 'required|min:10|max:11',
            'address' => 'required|max:255',
        ]);
        $check = $user->count();
        if ($check >= 1){
            if($request->password){
                $user->update([
                    'password'=>Hash::make($request->password),
                    'name'=>$request->user,
                    'landlinePhone'=>$request->landlinePhone,
                    'shaba'=>$request->shaba,
                ]);
                Event::create([
                    'type' => 9,
                    'title' => 'تغییر رمز',
                    'user_id' => auth()->user()->id,
                    'description' => 'کاربر با نام ' . auth()->user()->name . 'رمز خود را عوض کرد',
                ]);
            }else{
                $user->update([
                    'name'=>$request->user,
                    'landlinePhone'=>$request->landlinePhone,
                    'shaba'=>$request->shaba,
                ]);
            }
            $user->userMeta()->update([
                'date'=>$request->date,
                'name'=>$request->name,
                'post'=>$request->post,
                'job'=>$request->job,
                'code'=>$request->code,
                'address'=>$request->address,
            ]);
            Event::create([
                'type' => 11,
                'title' => 'تغییر اطلاعات کاربر',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . 'اطلاعات خود را تغییر داد',
            ]);
            return redirect('/profile/personal-info');
        }
        else{
            if($request->password){
                $user->update([
                    'number'=>$request->number,
                    'email'=>$request->email,
                    'password'=>Hash::make($request->password),
                    'name'=>$request->user,
                ]);
            }else{
                $user->update([
                    'number'=>$request->number,
                    'email'=>$request->email,
                    'name'=>$request->user,
                ]);
            }
            $userMeta = UserMeta::create([
                'date'=>$request->date,
                'name'=>$request->name,
                'post'=>$request->post,
                'job'=>$request->job,
                'code'=>$request->code,
                'address'=>$request->address,
            ]);
            Event::create([
                'type' => 11,
                'title' => 'تغییر اطلاعات کاربر',
                'user_id' => auth()->user()->id,
                'description' => 'کاربر با نام ' . auth()->user()->name . 'اطلاعات خود را تغییر داد',
            ]);
            $user->userMeta()->sync($userMeta->id);
            return redirect('/profile/personal-info');
        }
    }
    public function profile(){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('حساب کاربری - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if(auth()->user()){
            $pays = Pay::latest()->where('user_id' , auth()->user()->id)->take(10)->get();
            $likes =  auth()->user()->like;
            $likePost = [];
            foreach ($likes as $item) {
                $posts = Post::latest()->where('id' , $item->post_id)->first();
                array_push($likePost , $posts);
            }

            $bookmarks =  auth()->user()->bookmark;
            $bookmarkPost = [];
            foreach ($bookmarks as $item) {
                $posts = Post::latest()->where('id' , $item->post_id)->first();
                array_push($bookmarkPost , $posts);
            }
            return Inertia::render('User/UserIndex', [
                'title' => $title,
                'likePost' => $likePost,
                'pays' => $pays,
                'bookmarkPost' => $bookmarkPost,
            ]);
        }else{
            return redirect('/');
        }
    }
    public function pay(Request $request){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('پرداختی ها - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if(auth()->user()){
            $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
            if($request->show == 0){
                $pays = Pay::latest()->where('user_id' , auth()->user()->id)->paginate($showPostPage);
            }
            if($request->show == 1){
                $pays = Pay::latest()->where('user_id' , auth()->user()->id)->where('status' , 100)->paginate($showPostPage);
            }
            if($request->show == 2){
                $pays = Pay::latest()->where('user_id' , auth()->user()->id)->where('status' , '!=' , 100)->paginate($showPostPage);
            }
            if($request->show == 3){
                $pays = Pay::latest()->where('user_id' , auth()->user()->id)->where('deliver' , 1)->paginate($showPostPage);
            }
            if($request->show == 4){
                $pays = Pay::latest()->where('user_id' , auth()->user()->id)->where('deliver' , 0)->paginate($showPostPage);
            }
            return Inertia::render('User/PayUser', [
                'title' => $title,
                'pays' => $pays,
            ]);
        }else{
            return redirect('/');
        }
    }
    public function like(){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('علاقه مندی - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if(auth()->user()){
            $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
            $likes =  auth()->user()->like;
            $likePost = [];
            foreach ($likes as $item) {
                $posts = Post::latest()->where('id' , $item->post_id)->pluck('id')->first();
                array_push($likePost , $posts);
            }

            $likePosts = Post::latest()->whereIn('id' , $likePost)->paginate($showPostPage);
            return Inertia::render('User/LikeUser', [
                'title' => $title,
                'likePosts' => $likePosts,
            ]);
        }else{
            return redirect('/');
        }
    }
    public function bookmark(){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('نشانه ها - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if(auth()->user()){
            $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
            $bookmarks =  auth()->user()->bookmark;
            $bookmarkPost = [];
            foreach ($bookmarks as $item) {
                $posts = Post::latest()->where('id' , $item->post_id)->pluck('id')->first();
                array_push($bookmarkPost , $posts);
            }

            $bookmarkPosts = Post::latest()->whereIn('id' , $bookmarkPost)->paginate($showPostPage);
            return Inertia::render('User/BookmarkUser', [
                'title' => $title,
                'bookmarkPosts' => $bookmarkPosts,
            ]);
        }else{
            return redirect('/');
        }
    }
    public function comment(Request $request){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('دیدگاه ها - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if(auth()->user()){
            $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
            if($request->removeId){
                Comment::where('user_id' , auth()->user()->id)->where('id' , $request->removeId)->first()->delete();
            }
            if($request->show == 0){
                $comments = Comment::where('user_id' , auth()->user()->id)->with('post')->paginate($showPostPage);
            }
            if($request->show == 1){
                $comments = Comment::where('approved' , 0)->where('user_id' , auth()->user()->id)->with('post')->paginate($showPostPage);
            }
            if($request->show == 2){
                $comments = Comment::where('approved' , 1)->where('user_id' , auth()->user()->id)->with('post')->paginate($showPostPage);
            }
            return Inertia::render('User/CommentUser', [
                'title' => $title,
                'comments' => $comments,
            ]);
        }else{
            return redirect('/');
        }
    }
    public function ticket(Request $request){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('درخواست ها - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if(auth()->user()){
            $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();
            if($request->removeId){
                Ticket::where('id' , $request->removeId)->first()->delete();
            }
            if($request->show == 0){
                $tickets = Ticket::where('user_id' , auth()->user()->id)->paginate($showPostPage);
            }
            if($request->show == 1){
                $tickets = Ticket::where('answer', '!=' , null)->where('user_id' , auth()->user()->id)->paginate($showPostPage);
            }
            if($request->show == 2){
                $tickets = Ticket::where('answer' , null)->where('user_id' , auth()->user()->id)->paginate($showPostPage);
            }
            return Inertia::render('User/TicketUser', [
                'tickets' => $tickets,
            ]);
        }else{
            return redirect('/');
        }
    }
    public function recently(){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('بازدید های اخیر - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if(auth()->user()){
            $myViewsCheck = auth()->user()->view()->whereDate('created_at' , Carbon::today())->get();
            $showPostPage = Setting::where('key' , 'showPostPage')->pluck('value')->first();

            $views2 = [];
            for ( $i = 0; $i < count($myViewsCheck); $i++) {
                $views1 = $myViewsCheck[$i]->post()->pluck('id')->first();
                array_push($views2 ,$views1);
            }
            $views = Post::whereIn('id' , $views2)->with('review')->paginate($showPostPage);

            return Inertia::render('User/ViewUser', [
                'title' => $title,
                'views' => $views,
            ]);
        }else{
            return redirect('/');
        }
    }
    public function personalInfo(){
        $title = Setting::where('key' , 'title')->pluck('value')->first();
        $body = Setting::where('key' , 'body')->pluck('value')->first();
        $address = Setting::where('key' , 'address')->pluck('value')->first();
        $keyword = Setting::where('key' , 'keyword')->pluck('value')->first();
        $logo = Setting::where('key' , 'logo')->pluck('value')->first();
        OpenGraph::setDescription($body);
        OpenGraph::addProperty('locale', 'fa-ir');
        OpenGraph::setUrl($address);
        SEOTools::opengraph()->addProperty('type', 'stores');
        SEOMeta::addKeyword($keyword);
        OpenGraph::addImage($address.$logo);
        OpenGraph::setSiteName($title);
        TwitterCard::setTitle('اطلاعات شخصی - ' . $title);
        TwitterCard::setDescription($body);
        TwitterCard::setUrl($address);
        TwitterCard::addImage($address.$logo);
        if(auth()->user()){
            $user = User::where('id' , auth()->user()->id)->with('userMeta')->first();
            return Inertia::render('User/UserInfo', [
                'title' => $title,
                'user' => $user,
            ]);
        }else{
            return redirect('/');
        }
    }
    public function checkout(Request $request){
        $posts = Post::where('user_id' , auth()->user()->id)->latest()->where('status' , 1)->pluck('id')->toArray();
        $allPays = PayMeta::whereIn('post_id' , $posts)->where('status' , 100)->pluck('price')->sum();
        $checkSum = Checkout::where('user_id' , auth()->user()->id)->where('status' , 2)->latest()->pluck('price')->sum();
        if ($request->send){
            if (auth()->user()->shaba){
                Checkout::create([
                    'price' => (int)$allPays - (int)$checkSum,
                    'user_id' => auth()->user()->id,
                    'shaba' => auth()->user()->shaba,
                    'status' => 0,
                ]);
            }else{
                return redirect('/profile/personal-info');
            }
        }
        $check = Checkout::where('user_id' , auth()->user()->id)->latest()->get();
        $exist = Checkout::where('user_id' , auth()->user()->id)->where('status' , 0)->latest()->first();
        return Inertia::render('User/CheckoutIndex',[
            'check' => $check,
            'exist' => $exist,
            'checkSum' => $checkSum,
            'allPays' => $allPays,
        ]);
    }
    public function invoice(Pay $pay){
        if(auth()->user()){
            if ($pay->user_id == auth()->user()->id){
                $carrier = Carrier::with(["pay" => function ($q) use ($pay){
                    $q->where('id',$pay->id);
                }])->get();
                $title = Setting::where('key' , 'title')->pluck('value')->first();
                $logo = Setting::where('key' , 'logo')->pluck('value')->first();
                $address = Setting::where('key' , 'address')->pluck('value')->first();
                $email = Setting::where('key' , 'email')->pluck('value')->first();
                $number = Setting::where('key' , 'number')->pluck('value')->first();
                $pays = Pay::with('carrier','address')->where('id',$pay->id)->with(["payMeta" => function($q){
                    $q->with('guarantee','discount')->with(["post" => function($q){
                        $q->with('user');
                    }]);
                }])->with(["user" => function($q){
                    $q->with('userMeta');
                }])->first();
                return Inertia::render('User/InvoicePay', [
                    'pay' => $pays,
                    'title' => $title,
                    'number' => $number,
                    'email' => $email,
                    'address' => $address,
                    'logo' => $logo,
                    'carrier' => $carrier,
                ]);
            }else{
                return abort(404);
            }
        }else{
            return abort(404);
        }
    }
    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
