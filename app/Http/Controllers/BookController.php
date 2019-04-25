<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookCollection;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
    public function store(Request $request)
    {
        try{
          $book = new Book;
          $book->fill($request->all());
          $book->publisher_id = $request->publisher_id;
          $book->saveOrFail();
          $book->authors()->sync($request->authors);

          return response()->json([
            'id' => $book->id,
            'created_at' => $book->created_at,
          ]);
        }
        catch(QueryException $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ]);
        }
        catch(/Exception $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ]);
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
          $book = Book::with(['authors', 'publisher'])->find($id);
          if(!$book) throw new ModelNotFoundException;

          return BookResource($book);
        }
        catch(ModelNotFoundException $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ]);
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
    public function update(Request $request, $id)
    {
        try{
          $book = Book::find($id);
          if(!$book) throw new ModelNotFoundException;
          $book->fill($request->all());
          $book->saveOrFail();
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
        catch(/Exception $ex){
          return response()->json([
            'message' => $ex->getMessage(),
          ]);
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
        $book = Book::find($id);
        if(!$book) throw new ModelNotFoundException;
        $book->delete();
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
      catch(/Exception $ex){
        return response()->json([
          'message' => $ex->getMessage(),
        ]);
      }
    }
}
