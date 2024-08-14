<?php

namespace Tests\Unit\App;

use Bone\App\AppPackage;
use Codeception\Test\Unit;

class AppPackageTest extends Unit
{
    /**
     * @var \Tests\Support\UnitTester
     */
    protected $tester;

    /** @var AppPackage $package */
    protected $package;

    /**
     * @throws \Exception
     */
    protected function _before()
    {
        $this->package = new AppPackage();
    }

    protected function _after()
    {
        unset($this->package);
    }
}
