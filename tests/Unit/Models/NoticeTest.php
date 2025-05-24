<?php

namespace Tests\Unit;

use App\Models\Notice;
use Tests\TestCase;

class NoticeTest extends TestCase
{
    public function test_notice_status_pending_payment()
    {
        $notice = new Notice;
        $notice->status = Notice::STATUS_PENDING_PAYMENT;

        $this->assertEquals('pending_payment', $notice->status);
    }

    public function test_notice_status_served()
    {
        $notice = new Notice;
        $notice->status = 'served';

        $this->assertEquals('served', $notice->status);
    }
}
