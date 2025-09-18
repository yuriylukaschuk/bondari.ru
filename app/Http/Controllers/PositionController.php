<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    private string $err = '';

    public static function getPositions($params = []){

        if (empty($params['positions_id'])){
            $positions = Position::all();
        } else {
            $positions = Position::where('id','=', $params['positions_id'])->get();
        }

        return $positions->keyBy('id');
    }
}
