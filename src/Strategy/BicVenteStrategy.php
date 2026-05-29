<?php

namespace Urssaf\Strategy;

class BicVenteStrategy extends AbstractActivityStrategy
{

    public function cotisationRate(): float
    {
        return 22 / 100;
    }
    public function abatementRate(): float
    {
        return 71 / 100;
    }
    public function taxDischargePayment(): float
    {
        return 1 / 100;
    }

    public function specificSubsidy(float $caHt): float
    {
        if ($caHt > 3000) {
            return 200;
        } else {
            return 0;
        }
    }
}
