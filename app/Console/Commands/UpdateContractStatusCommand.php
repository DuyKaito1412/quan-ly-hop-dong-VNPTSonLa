<?php

namespace App\Console\Commands;

use App\Jobs\UpdateContractStatusJob;
use Illuminate\Console\Command;

class UpdateContractStatusCommand extends Command
{
    protected $signature = 'contracts:update-status';
    protected $description = 'Cập nhật trạng thái hợp đồng (NEAR_EXPIRY, EXPIRED)';

    public function handle()
    {
        $this->info('Đang cập nhật trạng thái hợp đồng...');
        
        UpdateContractStatusJob::dispatch();
        
        $this->info('Hoàn thành!');
    }
}
