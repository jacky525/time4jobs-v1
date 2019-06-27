<?php
// 參照 https://codeception.com/docs/03-AcceptanceTests

class FirstCest
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->amOnPage('/hello');
        $I->see('hello world');
    }
}
