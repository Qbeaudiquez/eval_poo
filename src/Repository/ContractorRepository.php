<?php

/**
 * Couche d'abstraction sur l'origine des données.
 */

namespace Urssaf\Repository;

use Urssaf\Model\Contractor;

class ContractorRepository
{
    //Injection de dépendance dans le constructeur de l'instance PDO (accès a la base de données)
    public function __construct(private \PDO $pdo) {}


    /**
     * Retourne l'identifiant généré par la base pour le nouveau record
     * @throws \Exception Si l'insertion en base de données échoue
     * @return int
     */
        public function save(string $fullName, string $siret, string $activity, string $taxSystem): int
    {
        try{
            $stmt = $this->pdo->prepare(
                "INSERT INTO contractor (
                    full_name,
                    siret,
                    activity,
                    tax_system) 
                VALUES (
                    :full_name,
                    :siret,
                    :activity,
                    :tax_system)");
            $stmt->execute([
                ":full_name" => $fullName,
                ":siret" => $siret,
                ":activity" => $activity,
                ":tax_system" => $taxSystem]);
                return (int)$this->pdo->lastInsertId();
        }catch(\PDOException $e){

            if($e->getCode() === "23000"){
                echo "Le numero siret doit être unique";
                exit;
            }else{
                throw $e;
            }
            
        }
    }

    /**
     * @return Contractor|null
     */
    public function find(int $id): ?Contractor
    {
        
            $stmt = $this->pdo->prepare(
                "SELECT *
                FROM contractor
                WHERE id = :id");
            $stmt->execute([
                ":id" => $id]);
    
            $contractor = $stmt->fetch(\PDO::FETCH_ASSOC);

            if(!$contractor){
                return null;
            }else{
                return new Contractor(
                    $contractor['id'],
                    $contractor['full_name'],
                    $contractor['siret'],
                    $contractor['activity'],
                    $contractor['tax_system'],
                    $contractor['created_at']
                    );
            }

            
    }

    /**
     * @return array<Contractor>
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare(
                "SELECT *
                FROM contractor");
        $contractorsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $contractors = [];

        foreach ($contractorsData as $contractorData) {
            $contractor = new Contractor(
                $contractorData['id'],
                $contractorData['full_name'],
                $contractorData['siret'],
                $contractorData['activity'],
                $contractorData['tax_system'],
                $contractorData['created_at']
                );
            $contractors[] = $contractor;
        }

        return $contractors;
    }
}
