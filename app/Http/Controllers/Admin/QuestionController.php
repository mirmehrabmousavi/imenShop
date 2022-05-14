<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuestionController extends Controller
{
    public function index(Request $request){
        $checkEdits =  auth()->user()->getAllPermissions()->where('name' , 'ویرایش پرسش و پاسخ')->pluck('name');
        $checkDeletes =  auth()->user()->getAllPermissions()->where('name' , 'حذف پرسش و پاسخ')->pluck('name');
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
        if($request->value){
            DB::table('questions')->whereIn('id', $request->value)->delete();
        }
        if($request->body){
            $tax = Question::where('id' , $request->taxId)->first();
            $tax->update([
                'body'=> $request->body,
                'approved'=> $request->approved,
                'updated_at'=> Carbon::now(),
            ]);
        }

        if($request->taxId && !$request->body){
            $taxEdit = Question::where('id' , $request->taxId)->first();
        }else{
            $taxEdit = '';
        }

        if ($request->approveds === 0 || $request->approveds == 1){
            $approved = Question::where('approved',$request->approveds)->pluck('id')->toArray();
        }else{
            $approved = Question::latest()->pluck('id')->toArray();
        }
        if ($request->date){
            $date = Question::whereDate('created_at',$request->date)->pluck('id')->toArray();
        }else{
            $date = Question::latest()->pluck('id')->toArray();
        }

        $arrayFilter = array_intersect($date,$approved);
        $questions = Question::latest()->whereIn('id' , $arrayFilter)->paginate(30);
        $labels = ['#','آیدی','توضیحات','تاییدیه','تاریخ ثبت','عملیات'];
        Inertia::setRootView('admin');
        return Inertia::render('QuestionPanel' , [
            'labels' => $labels,
            'edits' => $edits,
            'deletes' => $deletes,
            'questions' => $questions,
            'taxEdit' => $taxEdit,
        ]);
    }
}
