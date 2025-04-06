<?php

namespace App\Http\Controllers;

use App\Models\Row;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $rows = Row::orderBy('date','desc')->get()->groupBy(function ($row) {
            return $row->date;
        });
        return view('table.index', compact('rows'));
    }
}
