<?php

namespace Urssaf\Strategy;

class BicStrategy extends AbstractActivityStrategy
{

    public function cotisationRate(): float
    {
        return 12.8 / 100;
    }
    public function abatementRate(): float
    {
        return 50 / 100;
    }
    public function taxDischargePayment(): float
    {
        return 1.7 / 100;
    }

    public function specificSubsidy(float $caHt): float
    {
        return 0;
    }
}
