<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    public function index(Request $request)
    {
       
        $dataList = Ticket::orderBy('id','desc')->get();
        return response()->json($dataList);
    }

    public function info(Request $request)
    {
       
        $dataInfo = Ticket::with(['user','comments', 'comments.user', 'comments.replies','comments.replies.user',])->where('id',$request->dataId)->first();
        if(!empty($dataInfo)) {
            $responseData=[
                  'errMsgFlag'=>false,
                  'msgFlag'=>true,
                  'errMsg'=>null,
                  'msg'=>null,
                  'dataInfo'=>$dataInfo
            ];  
         }
         else{
              $responseData=[
                  'errMsgFlag'=>true,
                  'msgFlag'=>false,
                  'errMsg'=>'Requested Data Not Found.',
                  'msg'=>null,
                  'dataInfo'=>$dataInfo
            ];
         }
  
         return response()->json($responseData,200);
    }

    public function comments(Request $request)
    {
        $request->validate([
            'comment' => 'required',
            'ticketId' => 'required',
        ]);
      
        $userId = Auth::guard('sanctum')->user()->id;
        $comment = TicketComment::create([
            'comment' => $request->comment,
            'ticket_id' =>$request->ticketId,
            'user_id' => $userId,
        ]);
        $responseData = [
            'errMsgFlag' => false,
            'msgFlag' => true,
            'msg' => 'Data inserted successfully.',
            'errMsg' => null,
          
        ];

        return response()->json($responseData);

       
    }
    public function updateStatus(Request $request){
        try {
            DB::beginTransaction();
    
            $dataInfo = Ticket::find($request->dataId);
            $isNew = false;
    
            if (empty($dataInfo)) {
                $dataInfo = new Ticket();
                $isNew = true;
            }
            
            $dataInfo->status = $request->status;
           
    
            if ($dataInfo->save()) {
                DB::commit();
    
                $responseData = [
                    'errMsgFlag' => false,
                    'msgFlag' => true,
                    'msg' => $isNew ? 'Status Update successfully.' : 'Status updated successfully.',
                    'errMsg' => null,
                    'task' => $dataInfo,
                ];
    
                return response()->json($responseData, $isNew ? 201 : 200);
            } else {
                DB::rollBack();
    
                return response()->json([
                    'errMsgFlag' => true,
                    'msgFlag' => false,
                    'msg' => null,
                    'errMsg' => $isNew ? 'Data insert failed. Please try again!' : 'Data update failed. Please try again!',
                ], 500);
            }
        } catch (\Exception $err) {
            DB::rollBack();
    
            return response()->json([
                'errMsgFlag' => true,
                'msgFlag' => false,
                'msg' => null,
                'errMsg' => 'Something went wrong. Please try again.',
                'errorDetails' => [
                    'message' => $err->getMessage(),
                    // Remove trace in production!
                ],
            ], 500);
        }
    }

    public function storeReply(Request $request)
{
    $request->validate([
        'parent_id' => 'required|integer',
        'content' => 'required|string|max:1000',
    ]);

    $reply = TicketComment::create([
        'parent_id' => $request->parent_id,
        'user_id' => Auth::guard('sanctum')->user()->id,
        'comment' => $request->content,
    ]);

    return response()->json(['reply' => $reply], 201);
}

}
