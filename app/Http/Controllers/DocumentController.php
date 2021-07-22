<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class DocumentController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validator
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'timestamp' => 'required|int',
            'folder_id' => 'required'
        ]);
        
        // Invalid request response
        if ($validator->fails()){
            return response()->json(['error' => true, $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $userId= JWTAuth::toUser($request->header('Authorization'))['id'];
        $folder = $this->user->documents()->firstOrNew([
            'id' => $request->id
        ]);
        $folder->name = $request->name;
        $folder->timestamp = $request->timestamp;
        $folder->owner_id = $userId;
        $folder->folder_id = $request->folder_id;
        $isSaved = $folder->save();

        if ($isSaved){
            $message = 'document created';
            $statusCode = Response::HTTP_OK;
        } else {
            $message = 'create document failed';
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return response()->json([
            'error' => $isSaved ? false : true,
            'message' => $message,
            'data' => $folder,
        ], $statusCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $document = $this->user->documents()->find($id);
        return response()->json([
            'error' => false,
            'message' => 'Success get document',
            'data' => $document
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->user->documents()->find($id)->delete();

        return response()->json([
            'error' => false,
            'message' => 'Success delete document'
        ], Response::HTTP_OK);
    }
}
