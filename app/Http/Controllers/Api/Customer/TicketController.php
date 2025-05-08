<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class TicketController extends Controller
{
    public function index(Request $request)
    {
        $userId =Auth::guard('sanctum')->id();
        $dataList = Ticket::where('user_id',$userId)->orderBy('id','desc')->get();
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

    public function dataInfoAddOrUpdate(Request $request)
    {
        try {
            DB::beginTransaction();
    
            $dataInfo = Ticket::find($request->dataId);
            $isNew = false;
    
            if (empty($dataInfo)) {
                $dataInfo = new Ticket();
                $isNew = true;
            }
            $userId = Auth::guard('sanctum')->user()->id;
            $dataInfo->subject = $request->subject;
            $dataInfo->description = $request->description;
            $dataInfo->category = $request->category;
            $dataInfo->priority = $request->priority;
            $dataInfo->subject = $request->subject;
            $dataInfo->ticketId = rand(111111,99999);
            if ($request->hasFile('attachement')) {
                $file = $request->file('attachement');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('tickets/attachments', $filename, 'public');
            
                $dataInfo->attachement = $path; 
            }
          
            $dataInfo->user_id = $userId ;
    
            if ($dataInfo->save()) {
                DB::commit();
    
                $responseData = [
                    'errMsgFlag' => false,
                    'msgFlag' => true,
                    'msg' => $isNew ? 'Data inserted successfully.' : 'Data updated successfully.',
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
    
    public function comments(Request $request)
    {
        $request->validate([
            'comment' => 'required|string',
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
    
    
    public function dataInfoDelete(Request $request)
    {
        try {
            DB::beginTransaction();

            $dataInfo = Ticket::find($request->dataId);

            if (empty($dataInfo)) {
                $responseData = [
                    'errMsgFlag' => true,
                    'msgFlag' => false,
                    'msg' => null,
                    'errMsg' => 'Data not found!',
                ];
                return response()->json($responseData, 404);
            }



            if ($dataInfo->delete()) {
                DB::commit();
                $responseData = [
                    'errMsgFlag' => false,
                    'msgFlag' => true,
                    'msg' => 'Data deleted successfully.',
                    'errMsg' => null,
                ];
                return response()->json($responseData, 200);
            } else {
                DB::rollBack();
                $responseData = [
                    'errMsgFlag' => true,
                    'msgFlag' => false,
                    'msg' => null,
                    'errMsg' => 'Data deletion failed. Please try again!',
                ];
                return response()->json($responseData, 500);
            }
        } catch (\Exception $err) {
            DB::rollBack();
            $responseData = [
                'errMsgFlag' => true,
                'msgFlag' => false,
                'msg' => null,
                'errMsg' => 'Something went wrong. Please try again.',
                'errorDetails' => [
                    'message' => $err->getMessage(),
                    'trace' => $err->getTraceAsString()
                ],
            ];
            return response()->json($responseData, 500);
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
