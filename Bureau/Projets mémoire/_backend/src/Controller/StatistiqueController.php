<?php

namespace App\Controller;

use App\Entity\Statistique;
use App\Repository\UserRepository;
use App\Repository\ModeleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\StatistiqueRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatistiqueController extends AbstractController
{

    private EntityManagerInterface $manager;
    private ModeleRepository $modelRepository;
    private UserRepository $userRepository;
    private StatistiqueRepository $statistiqueRepository;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $manager,
        ModeleRepository $modeleRepository,
        UserRepository $userRepository,
        StatistiqueRepository $statistiqueRepository
    ) {
        $this->manager = $manager;
        $this->modelRepository = $modeleRepository;
        $this->serialize = $serializer;
        $this->userRepository = $userRepository;
        $this->statistiqueRepository = $statistiqueRepository;
    }


    /**
     * @Route( "api/statistique/{id}", name="like", methods={"POST"} )
     */
    public function like($id)
    {
        $user= $this->getUser();
        
        $lesLikes = $this->statistiqueRepository->findBy(['model'=>$id]);
        
        foreach ($lesLikes as $like){
            if($like->getUser()->getId() == $user->getId()){
                $total = $this->totalLike($id);
                return $this->json(['total'=>$total]);
            }
        }
        $user= $this->getUser();
        $model = $this->modelRepository->findOneBy(['id'=>$id]);
        $statistique = new Statistique();
        $statistique->setUser($user);
        $statistique->setModel($model);
        $this->manager->persist($statistique);
        $this->manager->flush();
        $total = $this->totalLike($id);
        return $this->json(['total'=>$total+1],200);

    }

    /**
     * @Route( "api/statistique", name="myLike", methods={"GET"} )
     */
    public function mylikes()
    {
        $meslike =[];
        $userId= $this->getUser()->getId();
        $mesLikes = $this->statistiqueRepository->findBy(['user'=>$userId]);
        foreach($mesLikes as $like){
            $meslike[]= $like->getModel()->getId();
        }
        return $this->json($meslike);

    }


    /**
     * @Route( "api/statistique/{id}", name="unLike", methods={"PUT"} )
     */
    public function inLike($id)
    {
        $user= $this->getUser();
        
        $lesLikes = $this->statistiqueRepository->findBy(['model'=>$id]);
        $total = $this->totalLike($id);
        foreach ($lesLikes as $like){
            if($like->getUser()->getId() == $user->getId()){
                $this->manager->remove($like);
                $this->manager->flush();
                
                return $this->json(['total'=>$total-1]);
            }
        }
        return $this->json(['total'=>$total],200);

    }

    protected function totalLike($id)
    {
        $likes = $this->statistiqueRepository->findBy(['model'=>$id]);
        return count($likes);
    }

}