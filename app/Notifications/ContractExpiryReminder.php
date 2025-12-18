<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractExpiryReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Contract $contract,
        public int $daysUntilExpiry
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nhắc nhở: Hợp đồng {$this->contract->contract_no} sắp hết hạn")
            ->line("Hợp đồng {$this->contract->contract_no} sẽ hết hạn sau {$this->daysUntilExpiry} ngày.")
            ->line("Ngày hết hạn: {$this->contract->end_date->format('d/m/Y')}")
            ->action('Xem hợp đồng', route('contracts.show', $this->contract))
            ->line('Vui lòng xem xét gia hạn hợp đồng.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'contract_id' => $this->contract->id,
            'contract_no' => $this->contract->contract_no,
            'days_until_expiry' => $this->daysUntilExpiry,
            'end_date' => $this->contract->end_date->format('d/m/Y'),
            'message' => "Hợp đồng {$this->contract->contract_no} sẽ hết hạn sau {$this->daysUntilExpiry} ngày",
        ];
    }
}
