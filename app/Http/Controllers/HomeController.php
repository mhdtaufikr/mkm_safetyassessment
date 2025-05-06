<?php

namespace App\Http\Controllers;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\SapInventory;
use App\Models\InventoryUpdate;
use App\Models\AggregatedInventorySummary;

class HomeController extends Controller
{

    public function index(Request $request)
    {

        return view('home.index');
    }

    // AJAX endpoint for groupno
    public function getGroupnoBySloc(Request $request)
    {
        $groupnos = DB::table('sap_inventories')
            ->where('sloc', $request->sloc)
            ->select('groupno')->distinct()->pluck('groupno');

        return response()->json($groupnos);
    }

    // AJAX endpoint for material & description
    public function getMaterialsByGroupno(Request $request)
    {
        $materials = DB::table('sap_inventories')
            ->where('sloc', $request->sloc)
            ->where('groupno', $request->groupno)
            ->select('material', 'materialdescription')
            ->distinct()
            ->get();

        return response()->json($materials);
    }


        public function detail($id)
    {


        $dataDetail = AggregatedInventorySummary::where('sap_inventory_id', $id)->first();
        if (request()->ajax()) {
            // Subquery to get the latest created_at for each serial_number
            $latestDates = InventoryUpdate::select('serial_number as latest_serial_number', \DB::raw('MAX(created_at) as latest_date'))
                                        ->groupBy('serial_number');

            // Main query to join with subquery to get only the latest records for each serial_number
            $query = InventoryUpdate::with('inventory')
                                ->joinSub($latestDates, 'latest_dates', function ($join) {
                                    $join->on('inventory_updates.serial_number', '=', 'latest_dates.latest_serial_number')
                                            ->on('inventory_updates.created_at', '=', 'latest_dates.latest_date');
                                })
                                ->where('sap_inventory_id', $id);

            return DataTables::of($query)
                ->addColumn('serial_number', function ($row) {
                    return $row->serial_number;
                })
                ->addColumn('actqty', function ($row) {
                    return $row->actqty;
                })
                ->addColumn('remarks', function ($row) {
                    return $row->remarks;
                })
                ->make(true);
        }

        return view('home.detail',compact('dataDetail'), ['id' => $id]);
    }
    public function printView()
    {
        if (!session()->has('printData')) {
            return redirect()->route('home')->with('failed', 'Nothing to print.');
        }

        $printData = session('printData');
        return view('inventory.print', compact('printData'));
    }

   public function manualInput(Request $request)
{
    $sloc = $request->sloc;
    $groupno = $request->groupno;
    $material = $request->material;

    // Ambil data utama dari sap_inventories
    $inventory = DB::table('sap_inventories')
        ->where('sloc', $sloc)
        ->where('groupno', $groupno)
        ->where('material', $material)
        ->first();

    // Ambil log update berdasarkan sap_inventory_id (jika inventory ditemukan)
    $updates = [];
    if ($inventory) {
        $updates = DB::table('inventory_updates')
            ->where('sap_inventory_id', $inventory->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // Return view atau redirect dengan data (bebas kamu)
    return view('home.manual_input', compact('inventory', 'updates'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'sap_inventory_id' => 'required|integer|exists:sap_inventories,id',
        'actqty' => 'required|numeric|min:0',
        'serial_number' => 'required|string|max:45',
        'remarks' => 'nullable|string|max:255',
        'pic' => 'required|string|max:45',
        'checker' => 'required|string|max:45',
    ]);

    DB::table('inventory_updates')->insert([
        'sap_inventory_id' => $validated['sap_inventory_id'],
        'actqty' => $validated['actqty'],
        'serial_number' => $validated['serial_number'],
        'remarks' => $validated['remarks'],
        'pic' => $validated['pic'],
        'checker' => $validated['checker'],
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('home')->with('status', 'Inventory update saved!');
}



}
