<?php

namespace App\Http\Controllers;

use App\Book;
use App\User;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $isbn = $request->input('isbn');
      $title = $request->input('title');
      $year = $request->input('year');
      $author = $request->input('author');
      $publisher = $request->input('publisher');

      $books = Book::with(['authors','publisher'])
        ->whereHas('authors', function($query) use($author){
          return $query->where('name', 'like', "%$author%");
        })
        ->whereHas('publisher', function($query) use($publisher){
          return $query->where('name', 'like', "%$publisher%");
        })
        ->when($isbn, function($query) use ($isbn){
          return $query->where('isbn', $isbn);
        })
        ->when($title, function($query) use($title){
          return $query->where('title', 'like', "%$title%");
        })
        ->when($year, function($query) use($year){
          return $query->where('year', $year);
        })
        ->paginate(10);

      return new BookCollection($books);
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
    public function store(BookRequest $request)
    {
        try{
          $user = Auth::user();
          if($user->can('create', Book::class)){
          $book = new Book;
          $book->fill($request->all());
            $book->publisher_id = $request->publisher_id;
            $book->saveOrFail();
            $book->authors()->sync($request->authors);

            return response()->json([
              'id' => $book->id,
              'created_at' => $book->created_at,
            ], 201);
          }
          else{
            return response()->json(['message' => 'Unauthorized']);
          }
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
     * Display the specified resource.
     *
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
          $user = Auth::user();
          $book = Book::with(['authors', 'publisher'])->find($id);
          if(!$book) throw new ModelNotFoundException;
          if($user->can('view', $book)){
            return new BookResource($book);
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
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function edit(Book $book)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(BookRequest $request, $id)
    {
        try{
          $user = Auth::user();
          $book = Book::find($id);
          if(!$book) throw new ModelNotFoundException;
          if($user->can('update', $book)){
            $book->fill($request->all());
            $book->publisher_id = $request->publisher_id;
            $book->saveOrFail();
            $book->authors()->sync($request->authors);

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
     * @param  \App\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      try{
        $user = Auth::user();
        $book = Book::find($id);
        if(!$book) throw new ModelNotFoundException;
        if($user->can('delete', $book)){
          $book->delete();
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
