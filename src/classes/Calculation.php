<?php
/**
* Class for all the calculation operations.
*/

namespace App\Classes;

use Exception;

class Calculation
{
    private string  $unit;
    private string  $unitDepth;
    private float   $width;
    private float   $length;
    private float   $depth;
    private Connect $connection;

    /**
     * Initializes a new instance of the Calculation class.
     */
    public function __construct()
    {
        $this->unit = 'm';
        $this->unitDepth = 'cm';
        $this->width = 0;
        $this->length = 0;
        $this->depth = 0;
        $this->connection = new Connect();
    }

    /**
     * Gets width of the object.
     *
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Gets length of the object.
     *
     * @return float
     */
    public function getLength(): float
    {
        return $this->length;
    }

    /**
     * Gets depth of the object.
     *
     * @return float
     */
    public function getDepth(): float
    {
        return $this->depth;
    }

    /**
     * Gets measurement unit of the object.
     *
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * Gets depth unit of the object.
     *
     * @return string
     */
    public function getUnitDepth(): string
    {
        return $this->unitDepth;
    }

    /**
     * Gets DB connection for the object.
     *
     * @return Connect
     */
    public function getConnection(): Connect
    {
        return $this->connection;
    }

    /**
     * Set the measurement unit for the object.
     *
     * @param string $unit
     *
     * @return self
     */
    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Set the measurement unitId for the object.
     *
     * @param int  $unitId
     * @param bool $depthUnit
     *
     * @return bool
     */
    public function setUnitId(int $unitId, bool $depthUnit = false): bool
    {
        $unitName = $this->getConnection()->findOneBy(
            ['unit_id' => $unitId],
            'units',
            'unit_short_name'
        );

        if ($unitName === null || empty($unitName[0]['unit_short_name'])) {
            return false;
        }

        if ($depthUnit) {
            $this->setUnitDepth($unitName[0]['unit_short_name']);
        } else {
            $this->setUnit($unitName[0]['unit_short_name']);
        }

        return true;
    }

    /**
     * Set the depth unit for the object.
     *
     * @param string $unitDepth
     *
     * @return self
     */
    public function setUnitDepth(string $unitDepth): self
    {
        $this->unitDepth = $unitDepth;

        return $this;
    }

    /**
     * Set the dimensions for the object.
     *
     * @param float $width
     * @param float $length
     * @param float $depth
     *
     * @return self
     */
    public function setDimensions(float $width, float $length, float $depth): self
    {
        $this->setWidth($width);
        $this->setLength($length);
        $this->setDepth($depth);

        return $this;
    }

    /**
     * Set the width for the object.
     *
     * @param float $width
     *
     * @return self
     */
    public function setWidth(float $width): self
    {
        $this->width = $this->convert($width, $this->getUnit());

        return $this;
    }

    /**
     * Set the width for the object.
     *
     * @param float $length
     *
     * @return self
     */
    public function setLength(float $length): self
    {
        $this->length = $this->convert($length, $this->getUnit());

        return $this;
    }

    /**
     * Set the width for the object.
     *
     * @param float $depth
     *
     * @return self
     */
    public function setDepth(float $depth): self
    {
        $this->depth = $this->convert($depth, $this->getUnitDepth(), true);

        return $this;
    }

    /**
     * Converts the dimensions to the default unit (m - for measurement units, cm - for depth units).
     *
     * @param float  $value
     * @param string $unit
     * @param bool   $isDepth
     *
     * @return float
     */
    public function convert(float $value, string $unit, bool $isDepth = false): float
    {
        $neededUnit = $isDepth ? 'conversion_to_cm' : 'conversion_to_m';
        $dbVal = $this->getConnection()->findOneBy(
            ['unit_short_name' => $unit],
            'units',
            $neededUnit
        );

        return $value * (float) $dbVal[0][$neededUnit] ?? 0;
    }

    /**
     * Returns soil bag number for the current object.
     *
     * @return int
     */
    public function getBagsNumber(): int
    {
        return ceil($this->getDepth() * ($this->getWidth() * $this->getLength() * 0.025));
    }

    /**
     * Returns soil bag price for the current object.
     *
     * @param bool $incVat
     * @param float $vatRate
     *
     * @return float
     */
    public function getBagsPrice(bool $incVat = true, float $vatRate = 20): float
    {
        $soilBagPrice = $this->getConnection()->getSoilPrice();

        return $this->getBagsNumber() * ($incVat ? $soilBagPrice : $soilBagPrice / ((100 + $vatRate) / 100));
    }

    /**
     * Saves calculation into the DB.
     *
     * @return array|bool|string
     *
     * @throws Exception
     */
    public function saveCalculation()
    {
        $connection = $this->getConnection();

        $unit = $connection->findOneBy(
            ['unit_short_name' => $this->getUnit()],
            'units',
            'unit_id'
        );

        $depthUnit = $connection->findOneBy(
            ['unit_short_name' => $this->getUnitDepth()],
            'units',
            'unit_id'
        );

        if (empty($unit[0]['unit_id']) || empty($depthUnit[0]['unit_id'])) {
            throw new Exception('Could not find units. Please contact support.');
        }

        $newCalculation = [
            'width' => $this->getWidth(),
            'length' => $this->getLength(),
            'depth' => $this->getDepth(),
            'unit_id' => (int) $unit[0]['unit_id'],
            'depth_unit_id' => (int) $depthUnit[0]['unit_id'],
            'unit_price' => $connection->getSoilPrice(),
            'vat_rate' => $connection->getVatRate(),
        ];

        return $connection->insert($newCalculation, 'saved_calculations');
    }

    /**
     * Removes calculation from the DB.
     *
     * @param int $calculationId
     *
     * @return bool
     */
    public function removeCalculation(int $calculationId): bool
    {
        return $this->getConnection()->remove(
            ['calculation_id' => $calculationId],
            'saved_calculations'
        );
    }

    /**
     * Adds topsoil bags to the basket.
     *
     * @param int    $bagsNumber
     * @param string $basketCookie
     *
     * @return bool
     *
     * @throws Exception
     */
    public function addToBasket(int $bagsNumber, string $basketCookie): bool
    {
        $connection = $this->getConnection();
        if ($bagsNumber <= 0) {
            return false;
        }

        if (empty($basketCookie)) {
            throw new Exception('Couldn\'t add product to basket. Please contact support.');
        }

        $existingBasket = $connection->findOneBy(
            ['user_hash' => $basketCookie],
            'baskets',
            'bags_number'
        );

        if ($existingBasket === null || empty($existingBasket[0]['bags_number'])) {
            return $connection->insert(
                [
                    'bags_number' => $bagsNumber,
                    'user_hash' => $basketCookie,
                ],
                'baskets'
            );
        }

        return $connection->update(
            ['bags_number' => $bagsNumber + (int) $existingBasket[0]['bags_number']],
            'baskets',
            ['user_hash' => $basketCookie]
        );
    }

    /**
     * Returns the total amount of topsoil bags in the basket for the user.
     *
     * @param string $basketCookie
     *
     * @return int
     */
    public function getUserBasket(string $basketCookie): int
    {
        $connection = $this->getConnection();

        if (empty($basketCookie)) {
            return 0;
        }

        $existingBasket = $connection->findOneBy(
            ['user_hash' => $basketCookie],
            'baskets',
            'bags_number'
        );

        if ($existingBasket === null || empty($existingBasket[0]['bags_number'])) {
            return 0;
        }

        return (int) $existingBasket[0]['bags_number'];
    }

    /**
     * Removes topsoil bags from the basket.
     *
     * @param string $basketCookie
     *
     * @return bool
     */
    public function removeFromBasket(string $basketCookie): bool
    {
        return $this->getConnection()->remove(
            ['user_hash' => $basketCookie],
            'baskets'
        );
    }
}