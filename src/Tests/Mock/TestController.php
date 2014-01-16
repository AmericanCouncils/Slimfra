<?php

namespace Slimfra\Tests\Mock;

class TestController extends \Slimfra\Controller
{
    public function testRequest()
    {
        return $this['hello.name'];
    }
}
