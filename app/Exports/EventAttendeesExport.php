<?php
namespace App\Exports;

use App\Models\EventRegistration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EventAttendeesExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected int $eventId) {}

    public function collection()
    {
        return EventRegistration::with('member.club')
            ->where('event_id', $this->eventId)
            ->get();
    }

    public function headings(): array
    {
        return ['會員編號', '姓名（中文）', '姓名（英文）', '社團', '電話', '電子郵件', '報名狀態', '報到時間', '報名時間'];
    }

    public function map($reg): array
    {
        return [
            $reg->member?->member_id ?? '',
            $reg->member?->name_zh ?? '',
            $reg->member?->name_en ?? '',
            $reg->member?->club?->name ?? '',
            $reg->member?->phone ?? '',
            $reg->member?->email ?? '',
            match($reg->status) { 'registered' => '已報名', 'attended' => '已出席', 'cancelled' => '已取消', default => $reg->status },
            $reg->checked_in_at?->format('Y/m/d H:i') ?? '',
            $reg->created_at->format('Y/m/d H:i'),
        ];
    }
}
