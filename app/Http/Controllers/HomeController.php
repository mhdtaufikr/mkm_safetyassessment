<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\User;
use App\Models\RiskAssessment;
use App\Models\SaFinding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $activeUsers = User::where('last_login_at', '>=', Carbon::now()->subDay())->count();
        $status = $request->input('status');
        $month = $request->input('month', now()->format('m'));
        $year = $request->input('year', now()->format('Y'));

        $totalShops = Shop::count();

        $query = RiskAssessment::query();

        // Apply date filters
        $query->whereMonth('created_at', $month)
              ->whereYear('created_at', $year);

        // Apply status filter
        if ($status === 'closed') {
            $query->where('is_followed_up', true);
        } elseif ($status === 'open') {
            $query->where('is_followed_up', false);
        }

        // Total yang sesuai filter
        $totalAssessments = $query->count();

        // Risk Level Summary
        $rawCounts = clone $query;
        $rawCounts = $rawCounts->select('risk_level', DB::raw('count(*) as total'))
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level');

        $riskLevelCountsArray = [
            'Low' => 0,
            'Medium' => 0,
            'High' => 0,
            'Extreme' => 0,
        ];

        foreach ($rawCounts as $level => $total) {
            $riskLevelCountsArray[$level] = $total;
        }

        // Recent Assessments
        $recentAssessments = clone $query;
        $recentAssessments = $recentAssessments->with('shop')
            ->latest()
            ->take(10)
            ->get();

        // All Assessments
        $allAssessments = clone $query;
        $allAssessments = $allAssessments->with('shop', 'detail')
            ->latest()
            ->get();

        // Example data if needed for single form assessment
        $formAssessment = RiskAssessment::with('shop', 'detail')->find(31);

        return view('home.index', [
            'totalShops' => $totalShops,
            'totalAssessments' => $totalAssessments,
            'riskLevelCounts' => array_values($riskLevelCountsArray),
            'recentAssessments' => $recentAssessments,
            'allAssessments' => $allAssessments,
            'formAssessment' => $formAssessment,
            'month' => $month,
            'year' => $year,
            'user' => Auth::user(),
            'status' => $status,
            'activeUsers' => $activeUsers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'scope_number' => 'required|array',
            'finding_problem' => 'required|array',
            'potential_hazards' => 'required|array',
            'accessor' => 'required|array',
            'severity' => 'required|array',
            'possibility' => 'required|array',
            'score' => 'required|array',
            'risk_reduction_proposal' => 'required|array',
            'file.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:10240',
        ]);

        foreach ($request->finding_problem as $i => $problem) {
            $filePath = null;
            if ($request->hasFile("file.$i")) {
                $uploadedFile = $request->file("file")[$i];
                $filename = time() . '_' . $uploadedFile->getClientOriginalName();
                $filePath = $uploadedFile->storeAs('risk_files', $filename, 'public');
            }

            $score = $request->score[$i];
            $risk_level = $this->determineRiskLevel($score);

            $header = RiskAssessment::create([
                'shop_id' => $request->shop_id,
                'scope_number' => $request->scope_number[$i],
                'finding_problem' => $problem,
                'potential_hazards' => $request->potential_hazards[$i],
                'accessor' => $request->accessor[$i],
                'severity' => $request->severity[$i],
                'possibility' => $request->possibility[$i],
                'score' => $score,
                'risk_level' => $risk_level,
                'risk_reduction_proposal' => $request->risk_reduction_proposal[$i],
                'file' => $filePath,
                'created_by' => auth()->id(),
                'is_followed_up' => false,
            ]);

            $header->detail()->create([
                'scope' => $request->scope_number[$i],
                'finding_problem' => $problem,
                'potential_hazard' => $request->potential_hazards[$i],
                'accessor' => $request->accessor[$i],
                'severity' => $request->severity[$i],
                'possibility' => $request->possibility[$i],
                'score' => $score,
                'risk_level' => $risk_level,
                'reduction_measures' => $request->risk_reduction_proposal[$i],
            ]);
        }

        return redirect('/home')->with('success', 'Risk assessment data has been saved.');
    }

    public function destroy($id)
    {
        $assessment = RiskAssessment::find($id);
        if ($assessment) {
            $assessment->delete();
        }

        $finding = SaFinding::where('id_assessment', $id)->first();
        if ($finding) {
            $finding->delete();
        }

        return redirect()->back()->with('success', 'Risk assessment and related data deleted successfully.');
    }

    private function determineRiskLevel($score)
    {
        if ($score <= 4) {
            return 'Low';
        } elseif ($score <= 9) {
            return 'Medium';
        } elseif ($score <= 16) {
            return 'High';
        } else {
            return 'Extreme';
        }
    }
}