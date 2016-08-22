<?php

use Faker\Factory as Faker;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    // /**
    //  * The base URL to use while testing the application.
    //  *
    //  * @var string
    //  */
    // protected $baseUrl = 'http://localhost';

    // *
    //  * Creates the application.
    //  *
    //  * @return \Illuminate\Foundation\Application
     
    // public function createApplication()
    // {
    //     $app = require __DIR__.'/../bootstrap/app.php';

    //     $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    //     return $app;
    // }

    protected $fake;
    protected $times = 1;

    function __construct()
    {
        $this->fake = Faker::create();
    }

    protected function times($count){
        $this->times = $count;
        return $this;
    }
}
