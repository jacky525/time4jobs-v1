<?php

use Codeception\Util\HttpCode;
use \Time4job\Controllers\TestController;

class FuncCest
{
    protected $container;
    /**
     * @var TestController
     */
    protected $target;

    public function _before(FunctionalTester $I)
    {
        $this->container = $I->getContainer();
        $this->target = $this->container->get('TestController');
    }
    /**
     *
     */
    protected function _after()
    {
        $this->container = null;
        $this->target = null;
    }
    // tests
    public function tryToTest(FunctionalTester $I)
    {
        // Act
        $I->amOnPage('hello');

        // Assert
        $I->seeResponseCodeIs(HttpCode::OK);
        $I->canSee($this->target->hello());
    }
}
