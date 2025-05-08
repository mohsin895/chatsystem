<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Helpers\Response;
use Illuminate\Support\Facades\DB ;
use Illuminate\Support\Facades\Auth ;

class UserController extends Controller
{
    public function info(): JsonResponse
    {
        DB::beginTransaction();
        try {
        $userId = Auth::guard('sanctum')->user()->id;

        // Retrieve user with relationship 'userInfo'
        $dataInfo = User::where('id', $userId)->first();
    

        if ($dataInfo) { 
          
            DB::commit();
            $msg = 'Data Found Success.';
            return Response::successResponse($msg,$dataInfo);


        } else {
           

            $msg = 'Requested Data Not Found.';
            return Response::failedResponse($msg,$dataInfo);
        }
      } catch (\Exception $err) {
            DB::rollBack();
            $msg = 'Something went wrong. Please try again.';
            return Response::errorResponse($err, $msg);
        }
    }
}
