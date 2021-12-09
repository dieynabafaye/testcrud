<?php

namespace App\Controller;

use App\Repository\ModeleRepository;
use App\Repository\TailleurRepository;
use App\Services\ModeleService;
use App\Services\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ModeleController extends AbstractController
{
    private $modeleService;
    private $validatorService;
    private $manager;
    private $modelRepository;
    private $tailleurRepo;

    public function __construct(ModeleService $modeleService,
                                ValidatorService $validatorService,
                                EntityManagerInterface $manager,
                                ModeleRepository $modeleRepository,
                                TailleurRepository $tailleurRepository)
    {
        $this->modeleService= $modeleService;
        $this->validatorService= $validatorService;
        $this->manager = $manager;
        $this->modelRepository = $modeleRepository;
        $this->tailleurRepo = $tailleurRepository;
    }


    public function AddModele(Request $request):  JsonResponse
    {
        $modele = $this->modeleService->NewModele($request);
        $this->validatorService->Validate($modele);
        $this->manager->persist($modele);
        $this->manager->flush();
        return new JsonResponse(["message"=>"modèle créé avec succès"], 200);
    }



    public function getModelByTailleur(int $id_T, int $id_M)
    {

        $model= $this->modelRepository->getModelByTailleur($id_T, $id_M);

               return $this->json($model);
    }

    /**
     * @Route( "api/models/tailleur", name="mesModels", methods={"GET"} )
     */
    public function getAllModelOfOneTailleur(){
        $iduser = $this->getUser()->getId();
        $models= $this->modelRepository->getModelTailleur($iduser);
        $data = [];
        foreach($models as $key => $model){
            $data[$key]["id"] = $model->getId();
            $data[$key]["libelle"] = $model->getLibelle();
            $medias = $model->getMedia();
            if(!empty($medias[0])){
            $data[$key]["image"] = "data:image/jpeg;base64,".$medias[0]->getFile();
            }else{
                $data[$key]["image"] = "../../assets/images/img.jpg";
            }
            
        }
        return $this->json($data);
    }

    public function UpdateModele(Request $request, $id): JsonResponse
    {

        $modele = $this->modeleService->UpdateModele($request, $id);
        if ($modele != 'failed'){
            $this->manager->persist($modele);
            $this->manager->flush();
            return new JsonResponse(["message" => "modifié avec succée"], 200);
        }
        return new JsonResponse(["message" => "Modification impossible"], 500);
    }
}
