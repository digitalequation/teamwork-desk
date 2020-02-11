<?php

namespace Digitalequation\TeamworkDesk\Tests;

use Orchestra\Testbench\TestCase;
use Digitalequation\TeamworkDesk\TeamworkDeskServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [TeamworkDeskServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
