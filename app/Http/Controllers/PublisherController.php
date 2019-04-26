<?php

namespace App\Http\Controllers;

use App\Publisher;
use App\User;
use App\Http\Requests\PublisherRequest;
use App\Http\Resources\PublisherCollection;
use App\Http\Resources\PublisherResource;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $name = $request->input('name');

      $publishers = Publisher::with('books')
        ->when($name, function($query) use($name){
          return $query->where('name', 'like', "%$name%");
        })
        ->paginate(10);

        return new PublisherCollection($publishers);
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
    public function store(PublisherRequest $request)
    {
        try{
          $user = User::Auth();
          if($user->can('create', Publisher::class)){
            $publisher = new Publisher;
            $publisher->fill($request->all());
            $publisher->saveOrFail();

            return response()->json([
              'id' => $publisher->id,
              'created_at' => $publisher->created_at
            ], 201);
          }
          else{
            return response()->json(['message' => 'Unauthorized']);
          }
        }
        catch(QueryException $ex){
          return reponse()->json([
            'message' => $ex->getMessage(),
          ], 500);
        }
        catch(\Exception $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
          $user = User::Auth();
          $publisher = Publisher::with('books')->find($id);
          if(!$publisher) throw new ModelNotFoundException;
          if($user->can('view', $publisher)){
            return new PublisherResource($publisher);
          }
          else{
            return response()->json(['message' => 'Unauthorized']);
          }
        }
        catch(ModelNotFoundException $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function edit(Publisher $publisher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function update(PublisherRequest $request, $id)
    {
      try{
        $user = User::Auth();
        $publisher = Publisher::find($id);
        if(!$publisher) throw new ModelNotFoundException;
        if($user->can('update', $publisher)){
          $publisher->fill($request->all());
          $publisher->saveOrFail();

          return response()->json(null, 204);
        }
        else{
          return response()->json(['message' => 'Unauthorized']);
        }
      }
      catch(ModelNotFoundException $ex) {
          return response()->json([
              'message' => $ex->getMessage(),
          ], 404);
      }
      catch(QueryException $ex) {
          return response()->json([
              'message' => $ex->getMessage(),
          ], 500);
      }
      catch(\Exception $ex) {
          return response()->json([
              'message' => $ex->getMessage(),
          ], 500);
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Publisher  $publisher
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
          $user = User::Auth();
          $publisher = Publisher::find($id);
          if(!$publisher) throw new ModelNotFoundException;
          if($user->can('delete', $publisher)){
            $publisher->delete();
            return response()->json(null, 204);
          }
          else{
            return response()->json(['message' => 'Unauthorized']);
          }
        }
        catch(ModelNotFoundException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 404);
        }
        catch(QueryException $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
        catch(\Exception $ex) {
            return response()->json([
                'message' => $ex->getMessage(),
            ], 500);
        }
    }
}
