<?php

/**
 * Description of FooBarCalculator
 *
 * @author David Bwire
 */
class FooBarCalculator
{

    /**
     * The amount available for shopping
     * 
     * @var float
     */
    private $initialCashAtHand;

    /**
     * The rate/100
     * 
     * @var float
     */
    private $inflationRate;

    /**
     * Cash Balance
     * 
     * @var float
     */
    private $currentCashAtHand;

    /**
     *
     * @var float
     */
    private $firstBarPrice;

    /**
     *
     * @var float
     */
    private $justBoughtBarPrice;

    /**
     *
     * @var int
     */
    private $unitsBought = 0;

    /**
     * 
     * Use constructor injection to ensure the variables are provided at all times
     *
     * @param float $initialCashAtHand
     * @param float $firstBarPrice
     * @param float $inflationRate
     */
    public function __construct($initialCashAtHand, $firstBarPrice,
            $inflationRate)
    {
        $this->setInitialCashAtHand($initialCashAtHand)
                ->setFirstBarPrice($firstBarPrice)
                ->setInflationRate($inflationRate);
    }

    public function buy()
    {
        if (
                (null === $this->getCurrentCashAtHand()) &&
                (null === $this->getJustBoughtBarPrice())
        ) {
            // the run loop is just about to start
            // know if the client can afford to buy the very first item before inflation kicks in
            $initialCashAtHand = $this->getInitialCashAtHand();
            $firstBarPrice = $this->getFirstBarPrice();
            if ($this->isAffordable($initialCashAtHand, $firstBarPrice)) {
                // let the client buy
                $this->recordNewPurchase(1);
                // record what he remaining with
                $cashBalance = ($initialCashAtHand - $firstBarPrice);
                $this->setCurrentCashAtHand($cashBalance)
                        ->setJustBoughtBarPrice($firstBarPrice);
            } else {
                // he wasn't able to buy anything
                // report and exit
                $this->shoppingReport();
                return;
            }
        }
        // so far client has bought 1 item
        // get the cash he currently has at hand
        $cashAtHand = $this->getCurrentCashAtHand();
        // calculate how much the next bar to pick  will cost
        $nextBarPrice = $this->calculateCostOfAcquiringNewBar($this->getJustBoughtBarPrice());
        // test if he can afford it
        if ($this->isAffordable($cashAtHand, $nextBarPrice)) {
            // he is rich!
            $this->recordNewPurchase(1);
            // record how much he bought at
            $this->setJustBoughtBarPrice($nextBarPrice);
            $cashBalance = ($this->getCurrentCashAtHand() - $this->getJustBoughtBarPrice());
            // update current cash at hand
            $this->setCurrentCashAtHand($cashBalance);
            // shop some more till you drop
            $this->buy();
        } else {
            // client has exhausted his cash reserves
            // report and exit
            $this->shoppingReport();
            return;
        }
    }

    public function calculateCostOfAcquiringNewBar($currentBarPrice)
    {
        return $this->inflate($currentBarPrice);
    }

    public function getUnitsBought()
    {
        return $this->unitsBought;
    }

    /**
     *
     * @param float $cashAtHand
     * @param float $barPrice
     * @return boolean
     */
    public function isAffordable($cashAtHand, $barPrice)
    {
        if ($cashAtHand >= $barPrice) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @param float $justBoughtBarPrice
     * @return float Cost of buying a new bar
     */
    public function inflate($justBoughtBarPrice)
    {
        $newBarPrice = $justBoughtBarPrice + ($justBoughtBarPrice * $this->getInflationRate());
        return $newBarPrice;
    }

    /**
     *
     * @param float $initialCashAtHand
     * @return \FooBarCalculator
     */
    public function setInitialCashAtHand($initialCashAtHand)
    {
        $this->initialCashAtHand = $initialCashAtHand;
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getInitialCashAtHand()
    {
        return $this->initialCashAtHand;
    }

    /**
     *
     * @param float $firstBarPrice
     * @return \FooBarCalculator
     */
    public function setFirstBarPrice($firstBarPrice)
    {
        $this->firstBarPrice = $firstBarPrice;
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getFirstBarPrice()
    {
        return $this->firstBarPrice;
    }

    /**
     *
     * @param float $inflationRate
     * @return \FooBarCalculator
     */
    public function setInflationRate($inflationRate)
    {
        $this->inflationRate = $inflationRate;
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getInflationRate()
    {
        return $this->inflationRate;
    }

    /**
     *
     * @param float $justBoughtBarPrice
     * @return \FooBarCalculator
     * @throws \Exception
     */
    public function setJustBoughtBarPrice($justBoughtBarPrice)
    {
        if (null === $this->getFirstBarPrice()) {
            throw new \Exception('Please set the InitialBarPrice before proceeding.');
        }
        $this->justBoughtBarPrice = $justBoughtBarPrice;
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getJustBoughtBarPrice()
    {
        return $this->justBoughtBarPrice;
    }

    /**
     *
     * @param float $cashBalance
     * @return \FooBarCalculator
     * @throws Exception
     */
    public function setCurrentCashAtHand($cashBalance)
    {
        if (null === $this->getInitialCashAtHand()) {
            throw new Exception('Please set the InitialCashAtHand before proceeding.');
        }
        $this->currentCashAtHand = $cashBalance;
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getCurrentCashAtHand()
    {
        return $this->currentCashAtHand;
    }

    /**
     *
     * @param int $numberOfFreshItemsBought
     * @return \FooBarCalculator
     */
    public function recordNewPurchase($numberOfFreshItemsBought)
    {
        $unitsBought = $this->getUnitsBought();
        if (empty($unitsBought)) {
            $this->unitsBought = (int) $numberOfFreshItemsBought;
        } else {
            $this->unitsBought = $unitsBought + (int) $numberOfFreshItemsBought;
        }
        return $this;
    }

    public function shoppingReport()
    {
        print 'You managed to buy ' . $this->getUnitsBought() . ' items' . PHP_EOL;

        print 'You are left with $' . $this->getCurrentCashAtHand() . PHP_EOL;

        print 'Done!' . PHP_EOL;
    }

}

$calculator = new FooBarCalculator(200, 1, 0.2);
$calculator->buy();


/*
 * OUTPUT >>>
 *
 * php FooBarCalculator.php
 *
 * You managed to buy 20 items
 *
 * You are left with $13.312000377626
 *
 * Done!
 *
 */
