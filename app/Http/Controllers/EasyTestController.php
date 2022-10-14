<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EasyTestController extends Controller
{


    /**
     * 建立一個新的控制器實例
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function Test(Request $request)
    {
        $args = $request->all();
        dd($args);
    }


}
