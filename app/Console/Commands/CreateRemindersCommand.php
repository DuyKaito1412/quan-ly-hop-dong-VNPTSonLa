<?php

namespace App\Console\Commands;

use App\Jobs\CreateRemindersJob;
use Illuminate\Console\Command;

class CreateRemindersCommand extends Command
{
    protected $signature = 'contracts:create-reminders';
    protected $description = 'Tạo reminders cho hợp đồng sắp hết hạn';

    public function handle()
    {
        $this->info('Đang tạo reminders...');
        
        CreateRemindersJob::dispatch();
        
        $this->info('Hoàn thành!');
    }
}
