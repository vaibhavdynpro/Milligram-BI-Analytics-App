<?php

namespace App\Http\Controllers;
//test
use Illuminate\Http\Request;
use App\Looker;

class LookerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('looker');
    }
    public function index()
    {
        $activeMenu = '34';
        $id = '1';
        $activeSubMenu = '0';
        $lookerData = Looker::find($id);
        
        return view('looker.edit',compact('lookerData','activeMenu','id','activeSubMenu'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $flight = Looker::updateOrCreate(
            ['id' => '1'],
            ['api_url' => $request->api_url, 
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'secret' => $request->secret,
            'host' => $request->host
            ]
        );
        return redirect('lookerSetting')->with('success','Data has been updated successfully!!');;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
