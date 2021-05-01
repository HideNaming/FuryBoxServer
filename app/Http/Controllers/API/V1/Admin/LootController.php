<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Models\Loot;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\UploadedFile;
use phpDocumentor\Reflection\Types\Boolean;

class LootController extends Controller

{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()

    {
        $this->middleware('permission:loot-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:loot-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:loot-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (!$request->input('perPage')) {
            return Loot::orderBy('id', 'asc')->get();
        }
        return Loot::orderBy($request->input('sortField'), $request->input('sortOrder'))->paginate($request->input('perPage'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Loot::create('name', 'name');
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
            'box_id' => 'required|numeric|exists:boxes,id',
        ]);

        $imageName = md5(uniqid(rand(), true)). '.' . $request->image->extension();
        $request->image->move(public_path('images'), $imageName);

        $requestData = $request->all();
        $requestData['image'] = $imageName;
        $requestData['stock'] = $requestData['stock'] === 'true' ? true : false;

        Loot::create($requestData);

        return true;
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Loot  $loot
     * @return \Illuminate\Http\Response
     */

    public function show(Loot $loot)
    {
        return $loot;
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Loot  $loot
     * @return \Illuminate\Http\Response
     */

    public function edit(Loot $loot)
    {
        return $loot;
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Loot  $loot
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, Loot $loot)
    {
        request()->validate([
            'name' => 'required',
            'box_id' => 'required|numeric|exists:boxes,id',
        ]);

        $requestData = $request->all();
        if ($request->hasFile('image')) {
            request()->validate([
                'image' => 'mimes:jpeg,jpg,png,gif,svg',
            ]);
            $imageName = md5(uniqid(rand(), true)). '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $requestData['image'] = $imageName;
        } else {
            $requestData['image'] = $loot['image'];
        }

        $requestData['stock'] = $requestData['stock'] === 'true' ? true : false;

        $loot->update($requestData);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Loot  $loot
     * @return \Illuminate\Http\Response
     */

    public function destroy(Loot $loot)
    {
        $loot->delete();

        return true;
    }
}
