<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pay;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Hekmatinasser\Verta\Verta;
use App\Models\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PanelController extends Controller
{
    public function index(){
        $users = User::get();
        foreach ($users as $user) {
            if (Cache::has('user-is-online-' . $user->id)){
                $user->update([
                    'activity' => Verta::now()->format('H:i Y-n-j')
                ]);
            }
        }


        $year2 =verta()->year;
        $farvardin = Verta::parse($year2 . ' فروردین 1')->formatGregorian('Y-m-d H:i:s');
        $ordibehesht = Verta::parse($year2 . ' اردیبهشت 1')->formatGregorian('Y-m-d H:i:s');
        $khordad = Verta::parse($year2 . ' خرداد 1')->formatGregorian('Y-m-d H:i:s');
        $tir = Verta::parse($year2 . ' تیر 1')->formatGregorian('Y-m-d H:i:s');
        $mordad = Verta::parse($year2 . ' مرداد 1')->formatGregorian('Y-m-d H:i:s');
        $shahrivar = Verta::parse($year2 . ' شهریور 1')->formatGregorian('Y-m-d H:i:s');
        $mehr = Verta::parse($year2 . ' مهر 1')->formatGregorian('Y-m-d H:i:s');
        $aban = Verta::parse($year2 . ' آبان 1')->formatGregorian('Y-m-d H:i:s');
        $azar = Verta::parse($year2 . ' آذر 1')->formatGregorian('Y-m-d H:i:s');
        $dey = Verta::parse($year2 . ' دی 1')->formatGregorian('Y-m-d H:i:s');
        $bahman = Verta::parse($year2 . ' بهمن 1')->formatGregorian('Y-m-d H:i:s');
        $esfand = Verta::parse($year2 . ' اسفند 1')->formatGregorian('Y-m-d H:i:s');

        $deyPay = Pay::whereBetween('created_at', [$dey, $bahman])->where('status' , '100')->pluck('price')->sum();
        $bahmanPay = Pay::whereBetween('created_at', [$bahman, $esfand])->where('status' , '100')->pluck('price')->sum();
        $esfandPay = Pay::whereBetween('created_at', [$esfand, $farvardin])->where('status' , '100')->pluck('price')->sum();
        $farvardinPay = Pay::whereBetween('created_at', [$farvardin, $ordibehesht])->where('status' , '100')->pluck('price')->sum();
        $ordibeheshtPay = Pay::whereBetween('created_at', [$ordibehesht, $khordad])->where('status' , '100')->pluck('price')->sum();
        $khordadPay = Pay::whereBetween('created_at', [$khordad, $tir])->where('status' , '100')->pluck('price')->sum();
        $tirPay = Pay::whereBetween('created_at', [$tir, $mordad])->where('status' , '100')->pluck('price')->sum();
        $mordadPay = Pay::whereBetween('created_at', [$mordad, $shahrivar])->where('status' , '100')->pluck('price')->sum();
        $shahrivarPay = Pay::whereBetween('created_at', [$shahrivar, $mehr])->where('status' , '100')->pluck('price')->sum();
        $mehrPay = Pay::whereBetween('created_at', [$mehr, $aban])->where('status' , '100')->pluck('price')->sum();
        $abanPay = Pay::whereBetween('created_at', [$aban, $azar])->where('status' , '100')->pluck('price')->sum();
        $azarPay = Pay::whereBetween('created_at', [$azar, $dey])->where('status' , '100')->pluck('price')->sum();

        $lastPost = Post::latest()->take(10)->get();

        $lastComment = Comment::latest()->with('post','user')->take(5)->get();
        $acceptComment = Comment::latest()->where('approved' , 1)->count();
        $checkComment = Comment::latest()->where('approved' , 0)->count();

        $lastUser = User::latest()->take(5)->get();
        $onlineUser = User::latest()->where('activity' , Verta::now()->format('H:i Y-n-j'))->count();
        $offUser = User::latest()->where('activity' , '!=' , Verta::now()->format('H:i Y-n-j'))->count();

        $startDayEn = verta()->startDay()->formatGregorian('Y-m-d H:i:s');
        $endDayEn = verta()->endDay()->formatGregorian('Y-m-d H:i:s');
        $startYesterdayEn = verta()->subDay(1)->startDay()->formatGregorian('Y-m-d H:i:s');
        $endYesterdayEn = verta()->subDay(1)->endDay()->formatGregorian('Y-m-d H:i:s');
        $startMonthEn = verta()->startMonth()->formatGregorian('Y-m-d H:i:s');
        $endMonthEn = verta()->endMonth()->formatGregorian('Y-m-d H:i:s');
        $startWeekEn = verta()->startWeek()->formatGregorian('Y-m-d H:i:s');
        $endWeekEn = verta()->endWeek()->formatGregorian('Y-m-d H:i:s');
        $startYearEn = verta()->startYear()->formatGregorian('Y-m-d H:i:s');
        $endYearEn = verta()->endYear()->formatGregorian('Y-m-d H:i:s');

        $viewDay = View::whereBetween('created_at', [$startDayEn, $endDayEn])->count();
        $viewYesterday = View::whereBetween('created_at', [$startYesterdayEn, $endYesterdayEn])->count();
        $viewMonth = View::whereBetween('created_at', [$startMonthEn, $endMonthEn])->count();
        $viewWeek = View::whereBetween('created_at', [$startWeekEn, $endWeekEn])->count();
        $viewYear = View::whereBetween('created_at', [$startYearEn, $endYearEn])->count();

        Inertia::setRootView('admin');
        return Inertia::render('panel' , [
            'lastUser' => $lastUser,
            'onlineUser' => $onlineUser,
            'offUser' => $offUser,
            'deyPay' => $deyPay,
            'bahmanPay' => $bahmanPay,
            'esfandPay' => $esfandPay,
            'farvardinPay' => $farvardinPay,
            'ordibeheshtPay' => $ordibeheshtPay,
            'khordadPay' => $khordadPay,
            'tirPay' => $tirPay,
            'mordadPay' => $mordadPay,
            'shahrivarPay' => $shahrivarPay,
            'mehrPay' => $mehrPay,
            'abanPay' => $abanPay,
            'azarPay' => $azarPay,
            'acceptComment' => $acceptComment,
            'checkComment' => $checkComment,
            'lastPost' => $lastPost,
            'lastComment' => $lastComment,
            'viewDay' => $viewDay,
            'viewYesterday' => $viewYesterday,
            'viewMonth' => $viewMonth,
            'viewYear' => $viewYear,
            'viewWeek' => $viewWeek,
        ]);
    }
    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
