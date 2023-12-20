<?php

namespace Joemires\Superban\Tests\Feature;

use Joemires\Superban\Tests\TestCase;

class SuperbanTest extends TestCase
{
    public function testRouteIsProtectedBySuperban()
    {
        $loop = 0;

        while ($loop < 10) {
            $response = $this->get('/superban-protected');
            $loop++;
        }

        $response->assertTooManyRequests();
    }

    public function testRouteIsNotProtectedBySuperban()
    {
        $loop = 0;

        while ($loop < 10) {
            $response = $this->get('/superban-unprotected');
            $loop++;
        }

        $response->assertOk();
    }


}
