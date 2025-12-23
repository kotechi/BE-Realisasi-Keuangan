<?php

namespace App\Http\Controllers\API\Param;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\Param\ParamResource;
use App\Http\Resources\Param\ParticipantResource;
use App\Http\Resources\Unit\UnitDropdownResource;
use App\Http\Resources\User\UserUnitResource;
use App\Models\Param;
use App\Models\Participant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class ParamController extends Controller
{
    public function execution_unit()
    {
        return $this->param('execution_unit');
    }

    public function unit()
    {
        return $this->param('unit');
    }

    public function unit_dropdown(Request $request)
    {
        $query = Unit::query();

        // Add search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $units = $query->orderBy('name', 'asc')->get();

        return ResponseFormatter::success(
            UnitDropdownResource::collection($units), 
            'success get unit dropdown data'
        );
    }

    public function deputi_dropdown(Request $request)
    {
        $query = Unit::whereNull('parent_code')
                     ->orWhere('parent_code', '');

        // Add search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $units = $query->orderBy('name', 'asc')->get();

        return ResponseFormatter::success(
            UnitDropdownResource::collection($units), 
            'success get deputi dropdown data'
        );
    }

    public function asdep_dropdown(Request $request)
    {
        $query = Unit::whereNotNull('parent_code')
                     ->where('parent_code', '!=', '');

        // Filter by parent_code (deputi)
        if ($request->has('parent_code') && $request->parent_code) {
            $query->where('parent_code', $request->parent_code);
        }

        // Add search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $units = $query->orderBy('name', 'asc')->get();

        return ResponseFormatter::success(
            UnitDropdownResource::collection($units), 
            'success get asdep dropdown data'
        );
    }

    public function priority_program()
    {
        return $this->param('priority_program');
    }

    public function note_unit()
    {
        $user = User::where('role', 'unit')
        ->select(
            'users.id',
            'users.name',
            'users.unit_id',
            'params.order',
            'params.param'
        )
        ->join('params', 'users.unit_id', '=', 'params.id')
        ->orderBy('order', 'asc')
        ->get();
        
        return ResponseFormatter::success(UserUnitResource::collection($user), 'success get note unit data');
    }

    public function participant(Request $request) 
    {
        $request->validate([
            'type' => ['nullable', 'in:material_preparation']
        ]);

        $participant = Participant::orderBy('group_order', 'asc')->orderBy('order', 'asc');

        if($request->type == 'material_preparation') {
            $participant->whereIn('group', ['eselon I', 'staff ahli menteri', 'staff khusus menteri', 'inspektur', 'direktur utama blu']);
        }

        $result = $participant->get();

        return ResponseFormatter::success(ParticipantResource::collection($result), 'success get participant param data');
    }

    public function padanan_data_category()
    {
        return $this->param('padanan_data_category');
    }
    
    public function padanan_data_source()
    {
        return $this->param('padanan_data_source');
    }

    public function param($category)
    {
        $param = Param::where('category', $category)->orderBy('order', 'asc')->get();
        return ResponseFormatter::success(ParamResource::collection($param), `success get {$category} data`);
    }
}
