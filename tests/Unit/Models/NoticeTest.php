<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Notice;

class NoticeTest extends TestCase
{
    public function testNoticeStatusPendingPayment()
    {
        $notice = new Notice();
        $notice->status = Notice::STATUS_PENDING_PAYMENT;

        $this->assertEquals('pending_payment', $notice->status);
    }

    public function testNoticeStatusServed()
    {
        $notice = new Notice();
        $notice->status = 'served';

        $this->assertEquals('served', $notice->status);
    }
}
