<?php


namespace App\Services;
use App\Entity\User;
use App\Repository\ConfigRepository;

class AbonnementService
{

    private ConfigRepository $configRepository;

    /**
     * AbonnementService constructor.
     * @param ConfigRepository $configRepository
     */
    public function __construct( ConfigRepository $configRepository)
    {

        $this->configRepository = $configRepository;
    }

    /**
     * Permet de calculer la date de fin d'abonnement
     * @return string
     */
    public function Abonner(){
        // on recuper la durée de l'abonnement
        $configs = $this->configRepository->findAll();
        $duree = 0;
        foreach ($configs as $config){
            $duree = $config->getDuree();
        }
        $dateDebut = date('Y-m-d');
        return $this->calculDateFin($dateDebut,$duree);
    }

    private function calculDateFin( $dateDebut, int $duree){
        return date('Y-m-d', strtotime($dateDebut. '+'.$duree.' days'));
    }
}
