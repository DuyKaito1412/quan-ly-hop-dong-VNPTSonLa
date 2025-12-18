<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Solution;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('solution');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('solution_id')) {
            $query->where('solution_id', $request->solution_id);
        }

        if ($request->filled('is_active')) {
            if ($request->is_active === '1') {
                $query->where('is_active', true);
            } elseif ($request->is_active === '0') {
                $query->where('is_active', false);
            }
        }

        $isGrouped = $request->boolean('group_by_solution');
        $solutions = Solution::orderBy('name')->get();
        
        if ($isGrouped) {
            $services = $query->orderBy('name')->get();
        } else {
            $services = $query->orderBy('name')->paginate(20)->withQueryString();
        }
        
        return view('services.index', compact('services', 'solutions', 'isGrouped'));
    }

    public function create()
    {
        $solutions = Solution::where('is_active', true)->orderBy('name')->get();

        return view('services.create', compact('solutions'));
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();

        if (empty($data['solution_id'])) {
            $data['solution_id'] = Solution::where('code', 'UNCAT')->value('id');
        }

        Service::create($data);
        
        return redirect()->route('services.index')
            ->with('success', 'Dịch vụ đã được tạo thành công.');
    }

    public function show(Service $service)
    {
        $service->load('solution');

        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $solutions = Solution::where('is_active', true)->orderBy('name')->get();

        $service->load('solution');

        return view('services.edit', compact('service', 'solutions'));
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();

        if (empty($data['solution_id'])) {
            $data['solution_id'] = Solution::where('code', 'UNCAT')->value('id');
        }

        $service->update($data);
        
        return redirect()->route('services.index')
            ->with('success', 'Dịch vụ đã được cập nhật thành công.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        
        return redirect()->route('services.index')
            ->with('success', 'Dịch vụ đã được xóa thành công.');
    }
}
