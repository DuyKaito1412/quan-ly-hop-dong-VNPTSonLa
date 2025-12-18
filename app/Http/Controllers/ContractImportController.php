<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportContractRequest;
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
use Maatwebsite\Excel\Facades\Excel;

class ContractImportController extends Controller
{
    public function show()
    {
        return view('contracts.import');
    }

    public function import(ImportContractRequest $request)
    {
        DB::beginTransaction();
        try {
            $file = $request->file('file');
            $duplicateAction = $request->input('duplicate_action', 'SKIP');
            
            $data = Excel::toArray([], $file);
            $rows = $data[0];
            
            // Bỏ qua header row
            array_shift($rows);
            
            $imported = 0;
            $skipped = 0;
            $updated = 0;
            $errors = [];
            
            foreach ($rows as $index => $row) {
                try {
                    $contractNo = $row[0] ?? null;
                    $customerCode = $row[1] ?? null;
                    $customerName = $row[2] ?? null;
                    $salesPersonEmail = $row[3] ?? null;
                    $title = $row[4] ?? null;
                    $startDate = $row[5] ?? null;
                    $endDate = $row[6] ?? null; // BẮT BUỘC
                    $serviceCode = $row[7] ?? null;
                    $serviceName = $row[8] ?? null;
                    $quantity = $row[9] ?? 1;
                    $unitPrice = $row[10] ?? 0;
                    $totalAmount = $row[11] ?? null;
                    $solutionCode = $row[12] ?? null;
                    $solutionName = $row[13] ?? null;
                    
                    // Validate end_date
                    if (empty($endDate)) {
                        $errors[] = "Dòng " . ($index + 2) . ": Thiếu end_date";
                        continue;
                    }
                    
                    // Tìm hoặc tạo Customer
                    $customer = Customer::firstOrCreate(
                        ['code' => $customerCode],
                        ['name' => $customerName ?? $customerCode]
                    );
                    
                    // Xác định Solution
                    $solutionId = $this->resolveSolutionId($solutionCode, $solutionName);

                    // Tìm hoặc tạo Service
                    $service = Service::firstOrCreate(
                        ['code' => $serviceCode],
                        [
                            'name' => $serviceName ?? $serviceCode,
                            'solution_id' => $solutionId,
                        ]
                    );

                    // Nếu service đã tồn tại nhưng chưa gán solution thì cập nhật
                    if (!$service->solution_id && $solutionId) {
                        $service->solution_id = $solutionId;
                        $service->save();
                    }
                    
                    // Tìm Sales Person
                    $salesPerson = null;
                    if ($salesPersonEmail) {
                        $salesPerson = User::where('email', $salesPersonEmail)->first();
                    }
                    
                    // Kiểm tra contract_no trùng
                    $existingContract = Contract::where('contract_no', $contractNo)->first();
                    
                    if ($existingContract) {
                        if ($duplicateAction === 'SKIP') {
                            $skipped++;
                            continue;
                        } elseif ($duplicateAction === 'UPDATE') {
                            // Update existing contract
                            $existingContract->update([
                                'customer_id' => $customer->id,
                                'sales_person_id' => $salesPerson?->id,
                                'title' => $title,
                                'start_date' => $this->parseDate($startDate),
                                'end_date' => $this->parseDate($endDate),
                                'total_amount' => $totalAmount ?? ($quantity * $unitPrice),
                            ]);
                            
                            // Update milestone
                            $milestone = $existingContract->milestones()->where('type', 'EXPIRY')->first();
                            if ($milestone) {
                                $milestone->update(['due_date' => $this->parseDate($endDate)]);
                            }
                            
                            $updated++;
                            continue;
                        }
                    }
                    
                    // Tạo contract mới
                    $contract = Contract::create([
                        'contract_no' => $contractNo,
                        'customer_id' => $customer->id,
                        'sales_person_id' => $salesPerson?->id,
                        'title' => $title,
                        'description' => null,
                        'start_date' => $this->parseDate($startDate),
                        'end_date' => $this->parseDate($endDate),
                        'status' => 'ACTIVE',
                        'total_amount' => $totalAmount ?? ($quantity * $unitPrice),
                        'currency' => 'VND',
                        'created_by' => Auth::id(),
                    ]);
                    
                    // Tạo contract item
                    ContractItem::create([
                        'contract_id' => $contract->id,
                        'service_id' => $service->id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'amount' => $quantity * $unitPrice,
                    ]);
                    
                    // Tạo milestone EXPIRY
                    Milestone::create([
                        'contract_id' => $contract->id,
                        'type' => 'EXPIRY',
                        'title' => 'Hết hạn hợp đồng',
                        'due_date' => $this->parseDate($endDate),
                        'done' => false,
                    ]);
                    
                    // Ghi status history
                    StatusHistory::create([
                        'contract_id' => $contract->id,
                        'from_status' => null,
                        'to_status' => 'ACTIVE',
                        'changed_by' => Auth::id(),
                        'changed_at' => now(),
                    ]);
                    
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Dòng " . ($index + 2) . ": " . $e->getMessage();
                }
            }
            
            DB::commit();
            
            $message = "Import hoàn tất: {$imported} hợp đồng mới";
            if ($updated > 0) {
                $message .= ", {$updated} hợp đồng được cập nhật";
            }
            if ($skipped > 0) {
                $message .= ", {$skipped} hợp đồng bị bỏ qua";
            }
            
            return redirect()->route('contracts.import.show')
                ->with('success', $message)
                ->with('errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    private function resolveSolutionId($solutionCode, $solutionName): ?int
    {
        $solutionCode = $solutionCode ? strtoupper(trim($solutionCode)) : null;
        $solutionName = $solutionName ? trim($solutionName) : null;

        if ($solutionCode) {
            $solution = Solution::whereRaw('UPPER(code) = ?', [$solutionCode])->first();
            if ($solution) {
                return $solution->id;
            }
        }

        // Không tự tạo Solution mới từ solution_name, map về UNCAT để đảm bảo ổn định
        $uncat = Solution::where('code', 'UNCAT')->first();

        return $uncat?->id;
    }

    public function downloadTemplate()
    {
        // Tạo file template Excel
        $headers = [
            'contract_no', 'customer_code', 'customer_name', 'sales_person_email',
            'title', 'start_date', 'end_date', 'service_code', 'service_name',
            'quantity', 'unit_price', 'total_amount', 'solution_code', 'solution_name'
        ];
        
        $data = [$headers];
        
        // Thêm một dòng mẫu
        $data[] = [
            'HD001', 'KH001', 'Công ty ABC', 'sales1@local',
            'Hợp đồng mẫu', '2024-01-01', '2024-12-31', 'DV001', 'Phát triển Website',
            1, 50000000, 50000000, 'DGS', 'TT Giải pháp Chính phủ số',
        ];
        
        $export = new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            public function __construct(public array $data) {}
            public function array(): array { return $this->data; }
        };
        
        return Excel::download($export, 'contract_template.xlsx');
    }

    private function parseDate($date)
    {
        if (is_numeric($date)) {
            // Excel date serial number
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
        }
        
        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}
