<?php

namespace Tests\Unit\Services;

use App\Services\User\GetPasswordResetRequestService;
use Tests\TestCase;

class GetPasswordResetRequestServiceTest extends TestCase
{
    public function testServiceCanBeInstantiated(): void
    {
        $service = new GetPasswordResetRequestService();

        $this->assertInstanceOf(GetPasswordResetRequestService::class, $service);
    }
}
