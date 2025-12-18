<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\UpdateContractRequest;
use App\Models\Attachment;
use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\Customer;
use App\Models\Milestone;
use App\Models\Service;
use App\Models\StatusHistory;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Contract::with(['customer', 'salesPerson']);
        
        // Phân quyền
        if ($user->hasRole('SALES')) {
            $query->where('sales_person_id', $user->id);
        } elseif (!$user->hasAnyRole(['ADMIN', 'MANAGER'])) {
            $query->whereRaw('1 = 0');
        }
        
        // Filters
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        if ($request->filled('sales_person_id')) {
            $query->where('sales_person_id', $request->sales_person_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('end_date_from')) {
            $query->where('end_date', '>=', $request->end_date_from);
        }
        
        if ($request->filled('end_date_to')) {
            $query->where('end_date', '<=', $request->end_date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_no', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }
        
        $contracts = $query->orderBy('end_date')->paginate(20);
        $customers = Customer::orderBy('name')->get();
        $salesPeople = User::where('is_sales_person', true)->get();
        
        return view('contracts.index', compact('contracts', 'customers', 'salesPeople'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $services = Service::with('solution')->where('is_active', true)->orderBy('name')->get();
        $solutions = Solution::where('is_active', true)->orderBy('name')->get();
        $salesPeople = User::where('is_sales_person', true)->get();
        
        return view('contracts.create', compact('customers', 'services', 'solutions', 'salesPeople'));
    }

    public function store(StoreContractRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $items = $data['items'];
            unset($data['items']);
            
            $data['created_by'] = Auth::id();
            $contract = Contract::create($data);
            
            // Tạo contract items
            $totalAmount = 0;
            foreach ($items as $index => $item) {
                $item['contract_id'] = $contract->id;
                $item['amount'] = $item['quantity'] * $item['unit_price'];
                $item['sort_order'] = $index;
                ContractItem::create($item);
                $totalAmount += $item['amount'];
            }
            
            // Cập nhật total_amount
            $contract->update(['total_amount' => $totalAmount]);
            
            // Tạo milestone EXPIRY
            Milestone::create([
                'contract_id' => $contract->id,
                'type' => 'EXPIRY',
                'title' => 'Hết hạn hợp đồng',
                'due_date' => $contract->end_date,
                'done' => false,
            ]);
            
            // Ghi status history
            StatusHistory::create([
                'contract_id' => $contract->id,
                'from_status' => null,
                'to_status' => $contract->status,
                'changed_by' => Auth::id(),
                'changed_at' => now(),
            ]);
            
            DB::commit();
            
            return redirect()->route('contracts.index')
                ->with('success', 'Hợp đồng đã được tạo thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function show(Contract $contract)
    {
        $this->authorize('view', $contract);
        
        $contract->load(['customer', 'salesPerson', 'items.service', 'attachments', 'milestones', 'statusHistory.changer', 'amendments']);
        
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $this->authorize('update', $contract);
        
        $customers = Customer::orderBy('name')->get();
        $services = Service::with('solution')->where('is_active', true)->orderBy('name')->get();
        $solutions = Solution::where('is_active', true)->orderBy('name')->get();
        $salesPeople = User::where('is_sales_person', true)->get();
        
        $contract->load('items');
        
        return view('contracts.edit', compact('contract', 'customers', 'services', 'solutions', 'salesPeople'));
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        $this->authorize('update', $contract);
        
        DB::beginTransaction();
        try {
            $oldEndDate = $contract->end_date;
            $oldStatus = $contract->status;
            
            $contract->update($request->validated());
            
            // Nếu end_date thay đổi, cập nhật milestone EXPIRY
            if ($contract->end_date != $oldEndDate) {
                $milestone = $contract->milestones()->where('type', 'EXPIRY')->first();
                if ($milestone) {
                    $milestone->update(['due_date' => $contract->end_date]);
                }
            }
            
            // Ghi status history nếu status thay đổi
            if ($contract->status != $oldStatus) {
                StatusHistory::create([
                    'contract_id' => $contract->id,
                    'from_status' => $oldStatus,
                    'to_status' => $contract->status,
                    'changed_by' => Auth::id(),
                    'changed_at' => now(),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('contracts.show', $contract)
                ->with('success', 'Hợp đồng đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    public function destroy(Contract $contract)
    {
        $this->authorize('delete', $contract);
        
        $contract->delete();
        
        return redirect()->route('contracts.index')
            ->with('success', 'Hợp đồng đã được xóa thành công.');
    }

    /**
     * Upload file đính kèm cho hợp đồng.
     */
    public function uploadAttachment(Request $request, Contract $contract)
    {
        $this->authorize('update', $contract);

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:20480', // tối đa ~20MB
            'description' => 'nullable|string|max:1000',
        ]);

        $file = $validated['file'];

        $path = $file->store("contracts/{$contract->id}", 'public');

        Attachment::create([
            'contract_id' => $contract->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'description' => $validated['description'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'File hợp đồng đã được tải lên thành công.');
    }

    /**
     * Xem trước file đính kèm trên trình duyệt (nếu trình duyệt hỗ trợ).
     */
    public function viewAttachment(Contract $contract, Attachment $attachment)
    {
        $this->authorize('view', $contract);

        // Đảm bảo file thuộc về đúng hợp đồng
        if ($attachment->contract_id !== $contract->id) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File không tồn tại.');
        }

        $mimeType = $attachment->file_type ?: Storage::disk('public')->mimeType($attachment->file_path);
        $fileName = $attachment->file_name;

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Nếu là PDF, stream trực tiếp
        if ($extension === 'pdf') {
            $stream = Storage::disk('public')->readStream($attachment->file_path);

            return response()->stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }

        // Nếu là DOC/DOCX, hiển thị qua Office Online Viewer (cần URL public)
        if (in_array($extension, ['doc', 'docx'])) {
            $fileUrl = asset(Storage::url($attachment->file_path));
            return view('contracts.attachment_preview', [
                'contract' => $contract,
                'attachment' => $attachment,
                'fileUrl' => $fileUrl,
            ]);
        }

        // Mặc định: cho tải về
        return Storage::disk('public')->download($attachment->file_path, $fileName);
    }
}
