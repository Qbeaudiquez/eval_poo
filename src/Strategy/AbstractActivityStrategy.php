<?php

/*
Ici on choisit une classe abstraite pour centraliser l'implémentation de la génération du rapport, commune à tous les régimes. On fait un mélange entre le pattern *Strategy* et *Template Method*
*/

namespace Urssaf\Strategy;

use Urssaf\Model\Contractor;

abstract class AbstractActivityStrategy
{
    //Retourne le rapport (qui sera affiché sur la sortie standard)
    public function buildReport(float $caHt, string $taxSystem, Contractor $contractor): string
    {
        $cotisations = $caHt * $this->cotisationRate();
        $indemnités = $this->specificSubsidy($caHt);

        $fullName = $contractor->getFullName();
        $activity = $contractor->getActivity();

        switch ($taxSystem) {
            case "ps":
                $caTtc = $caHt - $cotisations + $indemnités;
                $revenueImposable = $caHt * (1 - $this->abatementRate());
                $label = "Prélèvement à la source";
                return "{$fullName} | {$activity} - {$label}\n"
                    . "CA HT mensuel:        {$caHt}\n"
                    . "Aide spécifique:      {$indemnités}\n"
                    . "Cotisations sociales: {$cotisations}\n"
                    . "Revenu imposable:     {$revenueImposable}\n"
                    . "CA TTC mensuel:       {$caTtc}\n";
                break;
            case "vfl":
                $impot = $caHt * $this->taxDischargePayment();
                $caTtc = $caHt - $cotisations - $impot + $indemnités;
                $label = "Versement fiscal libératoire";
                return "{$fullName} | {$activity} - {$label}\n"
                    . "CA HT mensuel:                 {$caHt}\n"
                    . "Cotisations sociales:          {$cotisations}\n"
                    . "Montant de l'impôt à prélever: {$impot}\n"
                    . "CA TTC mensuel:                {$caTtc}\n";


                break;
            default:
                return "taxSystem invalid";
        }
    }
    //Retourne le taux de cotisation social
    abstract protected function cotisationRate(): float;
    //Retourne le taux de prélèvement dans le cadre du régime fiscal "Versement fiscal libératoire" (vfl)
    abstract protected function taxDischargePayment(): float;
    //Retourne le taux d'abattement fiscal dans le cadre du régime fiscal "Prélèvement à la source" (ps)
    abstract protected function abatementRate(): float;

    //De la logique métier propre à chaque régime d'activité
    //Calcul des indemnités de frais d'exploitation
    abstract protected function specificSubsidy(float $caHt): float;
}
