<?php

namespace App\Http\Controllers;

use App\Exports\Audit5SExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class Audit5SExportController extends Controller
{
    public function export($auditId)
    {
        return Excel::download(new Audit5SExport($auditId), 'audit_5s_' . $auditId . '.xlsx');
    }
}
