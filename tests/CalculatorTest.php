<?php
namespace Tests;

use App\Classes\Calculation;
use Exception;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * Tests dataset from the test assignment specification.
     */
    public function testCalculateNumberOfBags()
    {
        $this->assertEquals(4, ($this->createTestCalculation())->getBagsNumber());
    }

    /**
     * Tests conversion to m/cm.
     */
    public function testCalculationConversion()
    {
        $calculation = new Calculation();

        $cmToM = $calculation->convert(1, 'cm');
        $mToCm = $calculation->convert(1, 'm', true);
        $ftToM = $calculation->convert(1, 'ft');
        $ftToCm = $calculation->convert(1, 'ft', true);
        $inToM = $calculation->convert(1, 'in');
        $inToCm = $calculation->convert(1, 'in', true);
        $ydToM = $calculation->convert(1, 'yd');
        $ydToCm = $calculation->convert(1, 'yd', true);

        $this->assertEquals(0.01, $cmToM);
        $this->assertEquals(100, $mToCm);
        $this->assertEquals(0.3048, $ftToM);
        $this->assertEquals(30.48, $ftToCm);
        $this->assertEquals(0.0254, $inToM);
        $this->assertEquals(2.54, $inToCm);
        $this->assertEquals(0.9144, $ydToM);
        $this->assertEquals(91.44, $ydToCm);
    }

    /**
     * Tests price calculations.
     */
    public function testPriceCalculations()
    {
        $testCalculation = $this->createTestCalculation();
        $this->assertEquals(288, $testCalculation->getBagsPrice());
        $this->assertEquals(240, $testCalculation->getBagsPrice(false));
        $this->assertEquals(230.4, $testCalculation->getBagsPrice(false, 25));
    }

    /**
     * Tests calculation save and remove methods.
     *
     * @throws Exception
     */
    public function testCalculationCD()
    {
        $testCalculation = $this->createTestCalculation();
        $savedId = $testCalculation->saveCalculation();
        $this->assertEquals(!false, $savedId);
        $this->assertTrue($testCalculation->removeCalculation($savedId));
    }

    /**
     * Tests basket operations.
     *
     * @throws Exception
     */
    public function testBasketOperations()
    {
        $testCalculation = $this->createTestCalculation();
        $basketCookie = md5(uniqid());
        $this->assertEquals(!false, $testCalculation->addToBasket(1, $basketCookie));
        $this->assertEquals(!false, $testCalculation->addToBasket(1, $basketCookie));
        $this->assertEquals(2, $testCalculation->getUserBasket($basketCookie));
        $this->assertTrue($testCalculation->removeFromBasket($basketCookie));
    }

    /**
     * Creates and returns test calculation object with test dataset.
     *
     * @return Calculation
     */
    private function createTestCalculation(): Calculation
    {
        $calculation = new Calculation();
        $calculation->setUnit('m');
        $calculation->setUnitDepth('cm');
        $calculation->setDimensions(11, 10, 1.4);

        return $calculation;
    }
}