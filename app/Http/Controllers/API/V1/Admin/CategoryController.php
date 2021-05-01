<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;

class CategoryController extends Controller

{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()

    {
        $this->middleware('permission:category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (!$request->input('perPage')) {
            return Category::orderBy('id', 'asc')->get();
        }
        return Category::orderBy($request->input('sortField'), $request->input('sortOrder'))->paginate($request->input('perPage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Category::create('name', 'name');
        return compact('roles');
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)

    {
        request()->validate([
            'name' => 'required',
            'image' => 'required',
        ]);

        $imageName = str_slug(pathinfo($request->image->getClientOriginalName())['filename']) . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $requestData = $request->all();
        $requestData['image'] = $imageName;

        Category::create($requestData);

        return true;
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */

    public function show(Category $category)
    {
        return $category;
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */

    public function edit(Category $category)
    {
        return $category;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Category $category)
    {
        request()->validate([
            'name' => 'required',
        ]);

        $requestData = $request->all();
        if ($request->hasFile('image')) {
            request()->validate([
                'image' => 'mimes:jpeg,jpg,png,gif,svg',
            ]);
            $imageName = str_slug(pathinfo($request->image->getClientOriginalName())['filename']) . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $requestData['image'] = $imageName;
        } else {
            $requestData['image'] = $category['image'];
        }

        $category->update($requestData);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */

    public function destroy(Category $category)
    {
        $category->delete();

        return true;
    }
}
