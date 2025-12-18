<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolutionRequest;
use App\Http\Requests\UpdateSolutionRequest;
use App\Models\Solution;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Solution::class);

        $query = Solution::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            if ($request->is_active === '1') {
                $query->where('is_active', true);
            } elseif ($request->is_active === '0') {
                $query->where('is_active', false);
            }
        }

        $solutions = $query->orderBy('name')->paginate(20);

        return view('solutions.index', compact('solutions'));
    }

    public function create()
    {
        $this->authorize('create', Solution::class);

        return view('solutions.create');
    }

    public function store(StoreSolutionRequest $request)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        Solution::create($data);

        return redirect()->route('solutions.index')
            ->with('success', 'Giải pháp đã được tạo thành công.');
    }

    public function show(Solution $solution)
    {
        $this->authorize('view', $solution);

        return view('solutions.show', compact('solution'));
    }

    public function edit(Solution $solution)
    {
        $this->authorize('update', $solution);

        return view('solutions.edit', compact('solution'));
    }

    public function update(UpdateSolutionRequest $request, Solution $solution)
    {
        $data = $request->validated();
        $data['code'] = strtoupper($data['code']);

        $solution->update($data);

        return redirect()->route('solutions.index')
            ->with('success', 'Giải pháp đã được cập nhật thành công.');
    }

    public function destroy(Solution $solution)
    {
        $this->authorize('delete', $solution);

        // Không xóa UNCAT để tránh mất default
        if ($solution->code === 'UNCAT') {
            return redirect()->route('solutions.index')
                ->with('error', 'Không thể xóa giải pháp mặc định (UNCAT).');
        }

        $solution->delete();

        return redirect()->route('solutions.index')
            ->with('success', 'Giải pháp đã được xóa thành công.');
    }
}


