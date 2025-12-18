<?php

namespace App\Http\Controllers;

use App\Exports\Audit5SExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class Audit5SExportController extends Controller
{

    public function exportAll(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date'
        ]);

        return Excel::download(new Audit5SExport($request->start_date, $request->end_date), 'audit_5s_all.xlsx');
    }
}
