<?php

declare(strict_types=1);

namespace FrontendTest\Unit\Page\Service;

use Frontend\Page\Service\PageService;
use Frontend\Page\Service\PageServiceInterface;
use PHPUnit\Framework\TestCase;

class PageServiceTest extends TestCase
{
    public function testWillInstantiate(): void
    {
        $service = new PageService();

        $this->assertInstanceOf(PageServiceInterface::class, $service);
    }
}