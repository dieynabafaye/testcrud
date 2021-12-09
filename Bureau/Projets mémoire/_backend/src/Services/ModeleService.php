<?php


namespace App\Services;


use App\Entity\Media;
use App\Entity\Modele;
use App\Entity\Tailleur;
use App\Repository\ModeleRepository;
use App\Repository\TailleurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ModeleService
{

    private SerializerInterface $serializer;
    private  $token ;
    private EntityManagerInterface $manager ;
    private TailleurRepository $tailleurRepo ;
    private ModeleRepository $modeleRepo ;

    /**
     * AddModeleService constructor.
     * @param SerializerInterface $serializer
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $manager
     * @param TailleurRepository $tailleurRepository
     * @param ModeleRepository $modeleRepository
     */
    public function __construct(SerializerInterface $serializer,
                                TokenStorageInterface $tokenStorage,
                                EntityManagerInterface $manager,
                                TailleurRepository $tailleurRepository,
                                ModeleRepository $modeleRepository)
    {

        $this->serializer = $serializer;
        $this->token = $tokenStorage;
        $this->manager = $manager;
        $this->tailleurRepo = $tailleurRepository;
        $this->modeleRepo = $modeleRepository;
    }


    /**
     * @param Request $request
     * @return
     */
    public function NewModele(Request $request)
    {
        $uploadedFile = $request->files->get('medias');

        $modeleReq = $request->request->all();

        $newModele = $this->serializer->denormalize($modeleReq, Modele::class,true);
        $tailleur= $this->token->getToken()->getUser();
        $newModele->setTailleur($tailleur);
        $data = [];
        if($uploadedFile){
            foreach ($uploadedFile as $item){
                array_push($data,fopen($item->getRealPath(),'r+'));
            }
        }
        $newModele->setMedia($data);
        return $newModele;
    }

    public function UpdateModele($request, $id)
    {

        $modeleReq =json_decode( $request->getContent(),true);
        //dd($modeleReq);

     /*   $uploadedFile = $request->files->get('media');
        //dd($uploadedFile);
        $newModele = $this->modeleRepo->findOneBy(['id'=>$id]);
        $medias = $newModele->getMedia();
        //dd($newModele->getMedia()[1]);
        $j= count($uploadedFile);
        for($i = 0 ; $i < $j ; $i++){
            if(!empty($uploadedFile[$i]) ){
                if($medias[$i] ){
                    $file = $uploadedFile[$i]->getRealPath();
                    $modeleReq['media']= fopen($file,'r+');
                    //$media = new Media();
                    $medias[$i]->setFile($modeleReq['media']);
                    //$n->addMedium($media);
                }elseif(!$medias[$i] ){
                    $file = $uploadedFile[$i]->getRealPath();
                    $modeleReq['media']= fopen($file,'r+');
                    $media = new Media();
                    $media->setFile($modeleReq['media']);
                    $newModele->addMedium($media);
                }
            }else{
                $newModele->removeMedium($medias[$i]);
                $j++;


            }


        }*/

        $tailleur= $this->token->getToken()->getUser();
        $t= $this->tailleurRepo->findOneBy(['id'=>$tailleur->getId()]);
        foreach ($t->getModeles() as $modele){
          if ($modele->getId() == $id){
              $newModele = $this->modeleRepo->findOneBy(['id'=>$id]);


              $newModele->setLibelle($modeleReq['libelle']);
              $newModele->setDescription($modeleReq['description']);
              return $newModele;
          }
    }
        return 'failed';
    }


}
