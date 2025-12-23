<?php

namespace App\Http\Controllers\API\Realization;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Realization\RealizationParentResource;
use App\Http\Resources\Realization\RealizationResource;
use App\Imports\RealizationImport;
use App\Models\Realization;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class RealizationController extends Controller
{
    // Import dari file Excel
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:csv,xlsx,xls'],
        ]);

        $original_name = $request->file('file')->getClientOriginalName();
        $date = Carbon::parse(Str::substr($original_name, 0, 10))->format('Y-m-d');

        DB::transaction(function () use ($request, $date) {
            $day_realization = Realization::whereDate('date', $date);

            if($day_realization->count() > 0) {
                $day_realization->delete();
            }
            
            Excel::import(new RealizationImport($date), $request->file);
        });

        return ResponseFormatter::success(null, 'success import realization data');
    }

    // Input manual - create new realization
    public function create(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:191'],
            'budget' => ['required', 'numeric', 'min:0'],
            'aa' => ['required', 'numeric', 'min:0'],
            'budget_aa' => ['required', 'numeric', 'min:0'],
            'realization_spp' => ['required', 'numeric', 'min:0'],
            'sp2d' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
        ]);

        try {
            DB::beginTransaction();

            $realization = Realization::create([
                'code' => $request->code,
                'budget' => $request->budget,
                'aa' => $request->aa,
                'budget_aa' => $request->budget_aa,
                'realization_spp' => $request->realization_spp,
                'sp2d' => $request->sp2d,
                'date' => $request->date,
            ]);

            DB::commit();

            return ResponseFormatter::success(
                new RealizationResource($realization), 
                'success create realization data'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(
                null, 
                'failed to create realization data: ' . $e->getMessage(),
                500
            );
        }
    }

    // Get single realization by ID
    public function show($id)
    {
        try {
            $realization = Realization::findOrFail($id);
            
            return ResponseFormatter::success(
                new RealizationResource($realization), 
                'success get realization data'
            );
        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null, 
                'realization data not found',
                404
            );
        }
    }

    // Update data realization
    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => ['sometimes', 'string', 'max:191'],
            'budget' => ['sometimes', 'numeric', 'min:0'],
            'aa' => ['sometimes', 'numeric', 'min:0'],
            'budget_aa' => ['sometimes', 'numeric', 'min:0'],
            'realization_spp' => ['sometimes', 'numeric', 'min:0'],
            'sp2d' => ['sometimes', 'numeric', 'min:0'],
            'date' => ['sometimes', 'date'],
        ]);

        try {
            $realization = Realization::findOrFail($id);

            DB::beginTransaction();

            $realization->update($request->only([
                'code', 
                'budget', 
                'aa', 
                'budget_aa', 
                'realization_spp', 
                'sp2d', 
                'date'
            ]));

            DB::commit();

            return ResponseFormatter::success(
                new RealizationResource($realization), 
                'success update realization data'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return ResponseFormatter::error(
                null, 
                'failed to update realization data: ' . $e->getMessage(),
                500
            );
        }
    }

    // Delete data realization
    public function destroy($id)
    {
        try {
            $realization = Realization::findOrFail($id);
            $realization->delete();

            return ResponseFormatter::success(
                null, 
                'success delete realization data'
            );

        } catch (\Exception $e) {
            return ResponseFormatter::error(
                null, 
                'failed to delete realization data: ' . $e->getMessage(),
                500
            );
        }
    }

    public function total()
    {
        $realization = Realization::lastData()
        ->whereNull('parent_code');

        // Cek apakah ada data
        if ($realization->count() == 0) {
            return ResponseFormatter::success([
                'budget' => 0,
                'aa' => 0,
                'budget_aa' => 0,
                'realization_spp' => 0,
                'sp2d' => 0,
                'realization_spp_percent' => 0,
                'sp2d_percent' => 0,
                'sp2d_percent_aa' => 0,
                'date' => null,
            ], 'no realization data available');
        }

        $budget = $realization->sum('budget');
        $aa = $realization->sum('aa');
        $budget_aa = $realization->sum('budget_aa');
        $realization_spp = $realization->sum('realization_spp');
        $sp2d = $realization->sum('sp2d');
        $realization_spp_percent = ($budget_aa > 0) ? round($realization_spp / $budget_aa * 100, 2) : 0;
        $sp2d_percent = ($budget > 0) ? round($sp2d / $budget * 100, 2) : 0;
        $sp2d_percent_aa = ($budget_aa > 0) ? round($sp2d / $budget_aa * 100, 2) : 0;
        
        $first_realization = $realization->first();
        $date = $first_realization ? Carbon::parse($first_realization->date)->format('d-m-Y') : null;
        
        return ResponseFormatter::success([
            'budget' => $budget,
            'aa' => $aa,
            'budget_aa' => $budget_aa,
            'realization_spp' => $realization_spp,
            'sp2d' => $sp2d,
            'realization_spp_percent' => $realization_spp_percent,
            'sp2d_percent' => $sp2d_percent,
            'sp2d_percent_aa' => $sp2d_percent_aa,
            'date' => $date,
        ], 'success get total data realization');
    }

    public function total_by_periode(Request $request)
    {
        $request->validate([
            'limit' => ['nullable', 'integer'],
        ]);

        $limit = $request->input('limit', 10);

        $current_year = Carbon::parse(Carbon::now())->format('Y');

        $realization = Realization::joinUnit()
        ->select(
            DB::raw("(sum(budget)) as total_budget"),
            DB::raw("(sum(realization_spp)) as total_realization_spp"),
            DB::raw("(sum(aa)) as total_aa"),
            DB::raw("(sum(budget_aa)) as total_budget_aa"),
            DB::raw("(sum(sp2d)) as total_sp2d"),
            DB::raw("(DATE_FORMAT(date, '%d-%m-%Y')) as date")
        )
        ->whereNull('parent_code')
        ->whereYear('date', $current_year)
        ->orderByRaw('year(date) desc')
        ->orderByRaw('month(date) desc')
        ->orderByRaw('day(date) desc')
        ->groupBy(DB::raw("DATE_FORMAT(date, '%Y-%m-%d')"));

        $result = $realization->limit($limit)->get();
        return ResponseFormatter::success($result, 'success get total by periode data');
    }

    public function all(Request $request)
    {
        $request->validate([
            'filter' => ['required', 'in:eselon1,eselon2']
        ]);

        $realization = Realization::lastData()
        ->whereNull('parent_code')->get();

        $result = ($request->filter == 'eselon1') 
        ? RealizationResource::collection($realization) 
        : RealizationParentResource::collection($realization);
        
        return ResponseFormatter::success($result, 'success get all realization data');
    }
}