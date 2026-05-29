<?php

namespace Urssaf\Strategy;

class BncStrategy extends AbstractActivityStrategy
{

    public function cotisationRate(): float
    {
        return 22 / 100;
    }
    public function abatementRate(): float
    {
        return 34 / 100;
    }
    public function taxDischargePayment(): float
    {
        return 2.2 / 100;
    }

    public function specificSubsidy(float $caHt): float
    {
        if ($caHt < 1500) {
            return $caHt * 0.15;
        } else {
            return 0;
        }
    }
}
