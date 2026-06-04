<?php

/**
 * TEMPLATE DE DEPART
 * Application CLI cliente d'un système de gestion des autoentrepreneurs
 */

declare(strict_types=1);

//Chargement de l'auto-loader
require_once __DIR__ . '/vendor/autoload.php';

use Urssaf\Repository\ContractorRepository;
use Urssaf\Strategy\BicVenteStrategy;
use Urssaf\Strategy\BicStrategy;
use Urssaf\Strategy\BncStrategy;

// 1. Configuration PDO SQLite
$pdo = new PDO('sqlite:' . __DIR__ . '/urssafc.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 2. Création automatique de la table si elle n'existe pas
$pdo->exec("CREATE TABLE IF NOT EXISTS contractor (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    siret TEXT NOT NULL UNIQUE,
    activity TEXT NOT NULL,
    tax_system TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_DATE
)");

// 3. Instanciation du repository
$repo = new ContractorRepository($pdo);

// Extraction des arguments passés au script
$command = $argv[1] ?? null;

switch ($command) {
    case 'add':
        $fullName  = $argv[2] ?? null;
        $siret     = $argv[3] ?? null;
        $activity  = $argv[4] ?? null;
        $taxSystem = $argv[5] ?? null;

        if (!$fullName || !$siret || !$activity || !$taxSystem) {
            echo "Erreur: Arguments manquants pour 'add'.\n";
            exit(1);
        }

        // Validation du SIRET
        if (strlen($siret) !== 14 || !ctype_digit($siret)) {
            echo "Le SIRET n'est pas valide. Abandon.\n";
            exit(1);
        }

        $id = $repo->save($fullName, $siret, $activity, $taxSystem);
        echo "Autoentreprise enregistrée avec l'id: {$id}\n";
        break;

    case 'ls':
        $contractors = $repo->findAll();
        foreach ($contractors as $contractor) {
            echo "{$contractor->getId()} "
               . "{$contractor->getFullName()} "
               . "{$contractor->getSiret()} "
               . "{$contractor->getActivity()} "
               . "{$contractor->getTaxSysteme()}\n";
        }
        echo "Total: " . count($contractors) . "\n";
        break;

    case 'dry-declare':
        $id    = isset($argv[2]) ? (int)$argv[2] : null;
        $caHt  = isset($argv[3]) ? (float)$argv[3] : null;

        if (!$id || $caHt === null) {
            echo "Erreur: Arguments manquants pour 'dry-declare'.\n";
            exit(1);
        }

        // 1. Récupérer les données de l'auto-entreprise
        $contractor = $repo->find($id);
        if (!$contractor) {
            echo "Autoentreprise introuvable.\n";
            exit(1);
        }

        // 2. Injecter la bonne Strategy selon le régime d'activité
        $strategy = match($contractor->getActivity()) {
            'bic-vente' => new BicVenteStrategy(),
            'bic'       => new BicStrategy(),
            'bnc'       => new BncStrategy(),
            default     => throw new \Exception("Régime d'activité inconnu.")
        };

        // 3. Construire et afficher le rapport
        echo $strategy->buildReport($caHt, $contractor->getTaxSysteme(), $contractor);
        break;

    default:
        echo "Usage:\n";
        echo "  php urssafc.php add \"NOM_COMPLET\" SIRET REGIME_ACTIVITE REGIME_FISCAL\n";
        echo "  php urssafc.php ls\n";
        echo "  php urssafc.php dry-declare ID CA_HT\n";
        exit(1);
}