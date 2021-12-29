<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Option;

class OptionController extends Controller
{
    public function update(Request $request)
    {
        $options = $request->except(['_token']);

        if (count($options)) {
            foreach ($options as $key => $option)
                Option::option($key, $option);

            return response()->json(['status' => 'success']);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Empty data.'
        ]);
    }
}
