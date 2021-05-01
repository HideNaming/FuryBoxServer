<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Models\Box;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class BoxesController extends Controller

{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()

    {
        $this->middleware('permission:box-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:box-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:box-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (!$request->input('perPage')) {
            return Box::orderBy('category_id', 'asc')->get(['name', 'price', 'sale', 'category_id', 'image', 'slug']);
        }
        return Box::orderBy($request->input('sortField'), $request->input('sortOrder'))->paginate($request->input('perPage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Box::create('name', 'name');
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
            'image' => 'required|mimes:jpeg,jpg,png',
            'category_id' => 'required|numeric|exists:categories,id',
        ]);

        $imageName = str_slug(pathinfo($request->image->getClientOriginalName())['filename']) . '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $requestData = $request->all();
        $requestData['image'] = $imageName;
        $requestData['slug'] = Str::slug($requestData['name'], '-');

        Box::create($requestData);

        return true;
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        return Box::where('slug', '=', $id)->orWhere('id', '=', $id)->firstOrFail();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */

    public function edit(Box $box)
    {
        return $box;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Box $box)
    {
        request()->validate([
            'name' => 'required',
            'category_id' => 'required|numeric|exists:categories,id',
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
            $requestData['image'] = $box['image'];
        }

        $requestData['slug'] = Str::slug($requestData['name'], '-');

        $box->update($requestData);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Box  $box
     * @return \Illuminate\Http\Response
     */

    public function destroy(Box $box)
    {
        $box->delete();

        return true;
    }
}
