<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user= JWTAuth::toUser($request->header('Authorization'));
        $folders = $this->user->find($user['id'])
                                ->folders()
                                ->get();

        $documents = Document::where(function($query) use ($folders, $user){
                                $query->where('folder_id', $folders->where('is_public', 1)->pluck('id'))
                                    ->orWhere('company_id', $user['company_id']);
                            })
                            ->get(['id', 'name', 'type', 'owner_id', 'share', 'timestamp', 'company_id']);
                            
        return response()->json([
                                'error' => false,
                                'data' => $folders->merge($documents)
                            ], Response::HTTP_OK);
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
            'is_public' => 'boolean'
        ]);
        
        // Invalid request response
        if ($validator->fails()){
            return response()->json(['error' => true, $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $userId= JWTAuth::toUser($request->header('Authorization'))['id'];
        $folder = $this->user->folders()->firstOrNew([
            'id' => $request->id
        ]);
        $folder->name = $request->name;
        $folder->timestamp = $request->timestamp;
        $folder->owner_id = $userId;
        $folder->content = $request->content;
        $folder->share = $request->share;
        if ($request->is_public !== null) $folder->is_public = $request->is_public;
        $folder->company_id = $request->company_id;
        $isSaved = $folder->save();

        if ($isSaved){
            $message = 'folder created';
            $statusCode = Response::HTTP_OK;
        } else {
            $message = 'create folder failed';
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
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $document = $this->user->folders()->find($id)->documents()->get();
        return response()->json([
            'error' => false,
            'data' => $document
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Folder  $folder
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->user->folders()->find($id)->delete();

        return response()->json([
            'error' => false,
            'message' => 'Success delete folder'
        ], Response::HTTP_OK);
    }
}
