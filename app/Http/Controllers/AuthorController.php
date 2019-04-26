<?php

namespace App\Http\Controllers;

use App\Author;
use App\User
use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorCollection;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $name = $request->input('name');

      $authors = Author::with('books')
      ->when($name, function($query) use ($name){
        return $query->where('name', 'like', "%$name%");
      })
      ->paginate(10);

      return new AuthorCollection($authors);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AuthorRequest $request)
    {
        try{
          $user = Auth::user();
          if($user->can('create', Author::class)){
            $author = new Author;
            $author->fill($request->all());
            $author->saveOrFail();
            return response()->json([
              'id' => $author->id,
              'created_at' => $author->created_at,
            ], 201);
          }
          else{
            return response()->json(['message' => 'Unauthorized']);
          }
        }
        catch(QueryException $ex){
          return response()->json([
            'message' => $ex->getMessage()
          ], 500);
        }
        catch(\Exception $ex){
          return response()->json([
            'message' => $ex->getMessage()
          ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
          $user = User::Auth();
          $author = Author::with('books')->find($id);
          if(!$author) throw new ModelNotFoundException;
          if($user->can('view', $author)){
            return new AuthorResource($author);
          }
          else{
            return response()->json(['message' => 'Unauthorized']);
          }
        }
        catch(ModelNotFoundException $ex){
          return response()->json([
            'message' => $ex->getMessage()
          ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function edit(Author $author)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function update(AuthorRequest $request, $id)
    {
        try{
          $user = User::Auth();
          $author = Author::find($id);
          if(!$author) throw new ModelNotFoundException;
          if($user->can('update', $author)){
            $author->fill($request->all());
            $author->saveOrFail();

            return response()->json(null, 204);
          }
          else{
            return response()->json(['message' => 'Unauthorized']);
          }
        }
        catch(ModelNotFoundException $ex){
          return response()->json([
            'message' => $ex->message(),
          ], 404);
        }
        catch(QueryException $ex){
          return response()->json([
            'message' => $ex->message(),
          ], 500);
        }
        catch(\Exception $ex){
          return response()->json([
            'message' => $ex->message(),
          ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Author  $author
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
          $user = User::Auth();
          $author = Author::find($id);
          if(!$author) throw new ModelNotFoundException;
          if($user->can('delete', $author)){
            $author->delete();
            return response()->json(null, 204);
          }
          else{
            return response()->json(['message' => 'Unauthorized']);
          }
        }
        catch(ModelNotFoundException $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ]);
        }
        catch(QueryException $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ]);
        }
        catch(\Exception $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ]);
        }
    }
}
