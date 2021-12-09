<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;

use App\Services\AbonnementService;
use App\Services\UserService;
use App\Services\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    private EntityManagerInterface $manager;
    private SerializerInterface $serializer;
    private UserPasswordEncoderInterface $encode;
    private ProfilRepository $profilRepository;
    private UserService $userService;
    private UserRepository $utilisateurRepository;
    private ValidatorService $validatorService;
    private AbonnementService $abonnementService;


    /**
     * UserController constructor.
     * @param ProfilRepository $profilRepository
     * @param EntityManagerInterface $manager
     * @param SerializerInterface $serializer
     * @param UserRepository $utilisateurRepository
     * @param UserPasswordEncoderInterface $encode
     * @param UserService $userService
     * @param ValidatorService $validatorService
     * @param AbonnementService $abonnementService
     */
    public function __construct(ProfilRepository $profilRepository, EntityManagerInterface $manager,
                                SerializerInterface $serializer,
                                UserRepository $utilisateurRepository,
                                UserPasswordEncoderInterface $encode,
                                UserService $userService,
                                ValidatorService $validatorService,
                                AbonnementService $abonnementService
    )
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->encode =$encode;
        $this->profilRepository =$profilRepository;
        $this->userService =$userService;
        $this->utilisateurRepository =$utilisateurRepository;
        $this->validatorService =$validatorService;
        $this->abonnementService =$abonnementService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function Adduser(Request $request): JsonResponse
    {
        $utilisateur = $this->userService->NewUser($request);
        $this->validatorService->Validate($utilisateur);
        /*$dateFinAbonnement = $this->abonnementService->Abonner();
        dd($dateFinAbonnement);*/
        $this->manager->persist($utilisateur);
        $this->manager->flush();
        return new JsonResponse(["message"=>"ajouté avec succès"], 200);
    }

    public function UpdateUser(Request $request, $id): JsonResponse
    {
        $user = $this->getUser();
        if ($user->getId() == $id) {
        $userUpdate = $this->userService->GestionImage($request, 'avatar');
        $utilisateur = $this->utilisateurRepository->findOneBy(['id' => $id]);
        foreach ($userUpdate as $key => $valeur) {
            $setter = 'set' . ucfirst(strtolower($key));

            if (method_exists(User::class, $setter)) {

                if ($setter == 'setProfil') {
                    $utilisateur->setProfil($this->profilRepository->findOneBy(['libelle' => $userUpdate["profil"]]));
                } elseif ($setter == 'setPassword' && !empty($userUpdate['password'])) {
                    $utilisateur->setPassword($this->encode->encodePassword($utilisateur, $userUpdate['password']));
                } else {
                    $utilisateur->$setter($valeur);
                }
            }
        }
        $this->manager->flush();
        return new JsonResponse(["message" => "users update successfully"], 200);
        }else{
            return new JsonResponse(["message" => "impossible de modifier l'utilisateur"],500);
        }

    }

    /**
     * @Route( "api/users/verifPassword", name="verifPassword", methods={"POST"} )
     */
    public function VerifPassword(Request $request){
        $monPass =  $this->getUser()->getPassword();
        $userReq = $request->request->all()["password"];
        $isIdentique = false;
        $status = 500;
        if (password_verify($userReq,$monPass)){
            $isIdentique =true;
            $status = 200;
        }
        return new JsonResponse([$isIdentique],$status);
    }
    /**
     * @Route( "api/users/desabonner", name="desabonner", methods={"POST"} )
     */
    public function Desabonner(Request $request){
        $user = $this->getUser();
        $user->setDebutAbonnement(null);
        $user->setFinAbonnement(null);
        return new  JsonResponse([true],200);

    }
    /**
     * @Route( "api/users/sabonner", name="sabonner", methods={"POST"} )
     */
    public function Sabonner(){
        $dateFinAbonnement = $this->abonnementService->Abonner();
        $this->getUser()->setDebutAbonnement(new \DateTime('now'));
        $this->getUser()->setFinAbonnement(new \DateTime("$dateFinAbonnement"));
        $this->manager->flush();
        return new JsonResponse([true],200);
    }
}
