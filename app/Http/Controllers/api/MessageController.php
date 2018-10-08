<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\Message;
use App\QuestionAndAnswer;

use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function update(Request $request)
    {
    	Log::debug($request);
    	$msg = new Message();
    	$msg->mobile = $request['mobile'];
    	$msg->message = $request['message'];
    	$msg->who = $request['who'];
    	$msg->responder = $request['responder'];

    	$msg->save();

    	// if it's responding, update the processed status as true
    	if($request['who'] == 2) {
	    	$all_msg = Message::where('mobile', $request['mobile'])->update(['processed'=> true]);
    	}
    }

    public function show($mobile)
    {
    	Log::debug($mobile);

    	return Message::where('mobile', $mobile)->get();
    }

    public function showNew()
    {
    	return Message::select('mobile', DB::raw('count(*) as total'))->where('processed', false)->groupBy('mobile')->get();
    }

    public function showAll()
    {
        return Message::select('mobile', DB::raw('count(*) as total'))->groupBy('mobile')->get();
    }

    public function showAllNewCount()
    {
        $count= Message::select(DB::raw('count(*) as total'))->where('processed', false)->get();

        Log::debug($count);
        return $count;
    }

    public function showQnA()
    {
        return  QuestionAndAnswer::all();
    }

    public function updateQnA(Request $request)
    {
        $qna = new QuestionAndAnswer();
        $qna->question = $request['question'];
        $qna->answer = $request['answer'];

        $qna->save();
    }

    public function destroyQnA($id)
    {
        $qna = QuestionAndAnswer::find($id);

        $qna->delete();
    }

    public function getQnAById($id)
    {
        return QuestionAndAnswer::where('id', $id)->get();
    }
}
