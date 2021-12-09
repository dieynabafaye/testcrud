<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use App\Repository\ModeleRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use App\Repository\CommentaireRepository;
use App\Repository\StatistiqueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CommentaireController extends AbstractController
{

    private EntityManagerInterface $manager;
    private ModeleRepository $modelRepository;
    private ClientRepository $clientRepository;
    private DenormalizerInterface $serialize;
    private CommentaireRepository $commentaireRepository;

    public function __construct(
        SerializerInterface $serializer,
        EntityManagerInterface $manager,
        ClientRepository $clientRepository,
        ModeleRepository $modeleRepository,
        CommentaireRepository $commentaireRepository,
        UserRepository $userRepository,
        StatistiqueRepository $statistiqueRepository
    ) {
        $this->manager = $manager;
        $this->clientRepository = $clientRepository;
        $this->modelRepository = $modeleRepository;
        $this->serialize = $serializer;
        $this->commentaireRepository = $commentaireRepository;
        $this->userRepository = $userRepository;
        $this->statistiqueRepository = $statistiqueRepository;
    }


    public function addCommentaire(Request $request) {
        if (!$this->isGranted('ROLE_Admin')) {
            // On récupère le contenu du Json  puis on le décode
            $content = $request->getContent();
            $commentaire = $this->serialize->decode($content, 'json');
            // On récupère les infos du client
            $client = $this->getUser();
            // On instancie la date au moment du post du commentaire
            $date = new \DateTime();
            // On crée le commentaire en y insérant les données du client de la date ainsi que le contenu du Json
            $newComment = new Commentaire();
            $newComment->setDate($date);
            $newComment->setContenu($commentaire['contenu']);
            $newComment->setModele($this->modelRepository->findOneBy(['id' => $commentaire['modele_id']]));
            $newComment->setUser($client);

            $this->manager->persist($newComment);
            $this->manager->flush();
            return $this->json('Commentaire ajouté avec succès !');
        }else {
            return $this->json('Vous ne pouvez pas faire cette action désolé !');
        }
    }

    /**
     * @Route( "api/commentaire/{id}", name="updateCommentaire", methods={"PUT"} )
     */
    public function updateCommentaire(Request $request, int $id) {

        // Signaler un commentaire
        if ($this->isGranted('ROLE_Tailleur')) {
            // Récupérer le contenu du JSON
            $content = $request->getContent();
            // On récupère le tailleur qui fait le signalement
            $tailleur = $this->getUser();
            // On le  transforme en format JSON
            $commentaireJson = $this->serialize->decode($content, 'json');
            if ($commentaireJson['motifSignal'] === "") {
                return $this->json('Vous devez préciser la raison du signalement');
            } else {
                // On récupère le commentaire concerné via son id
                $commentaire = $this->commentaireRepository->findOneBy(['id' => $id]);
                // On modifie l'attribut isSignaled et on insère le motif du signalement
                $commentaire->setIsSignaled(true);
                $commentaire->setMotifSignal($commentaireJson['motifSignal']);

                $this->manager->persist($commentaire);
                $this->manager->flush();
                return $this->json('commentaire bien signalé !');
            }
        }elseif ($this->isGranted('ROLE_Client')) {
            // Modifier son propre commentaire
            $content = $request->getContent();
            $commentaire = $this->commentaireRepository->findOneBy(['id' => $id]);
            $commentaireJson = $this->serialize->decode($content, 'json');
            // On récupère l'id du client qui a faite le commentaire
            $idClientprecedent = $commentaire->getUser()->getId();
            // On récupère l'id du client qui fait la modification ensuite on compare les id
            $idClientModif = $this->getUser()->getId();

            if ($idClientprecedent === $idClientModif) {
                // On modifie le commentaire
                $commentaire->setContenu($commentaireJson['contenu']);
                $this->manager->persist($commentaire);
                $this->manager->flush();
                return $this->json('Commentaire modifié avec succès !');
            }else {
                return $this->json('Vous ne pouvez pas modifier ce commentaire !');
            }
        } else {
            return $this->json('Vous ne pouvez pas faire cette action désolé !');
        }
    }

    /**
     * @Route ( "api/commentaires/signaled", name="getSignaledComments", methods={"GET"} )
     */
    public function getSignaledComments() {
        if ($this->isGranted('ROLE_Admin')) {
            $commentaire = $this->commentaireRepository->findBy(['isSignaled' => true]);
            return $this->json($commentaire, 200);
        }else {
            return $this->json('Vous ne pouvez pas faire cette action désolé !');
        }
    }
    /**
     * @Route ( "api/commentaires/modeles/{id}", name="getAllCommentsForOneModel", methods={"GET"} )
     */
    public function getCommentsModel($id) {
            $data =[];
            $model = $this->modelRepository->findOneBy(['id' => $id]);
            $medias = $model->getMedia();
            $image = [];
            foreach($medias as $key => $media)
            {
                $image[$key]['id'] = $media->getId();
                $image[$key]['file'] = "data:image/jpeg;base64,".$media->getFile();
            }
            $data['medias'] = $image;
            
            $data['modeles']['id'] = $model->getId();
            $data['modeles']['libelle'] = $model->getLibelle();
            $data['modeles']['description'] = $model->getDescription();

            $tailleurId = $model->getTailleur()->getId();
            $tailleur = $this->userRepository->findOneBy(['id' => $tailleurId]);
            $data['tailleur']['id'] = $tailleur->getId();
            $data['tailleur']['nom'] = $tailleur->getPrenom()." ". $tailleur->getNom();
            $data['tailleur']['telephone'] = $tailleur->getTelephone();
            $commentaires = $model->getCommentaires();
            $result =[];
            foreach ($commentaires as $key =>  $commentaire) {
                if ($commentaire->getIsSignaled() === false) {
                    $userId = $commentaire->getUser()->getId();
                    $user = $this->userRepository->findOneBy(['id' => $userId]);
                    if ($commentaire->getIsSignaled() == false) {
                        $userId = $commentaire->getUser()->getId();
                        $user = $this->userRepository->findOneBy(['id' => $userId]);
                        $result[$key]['id'] = $commentaire->getId();
                        $result[$key]['contenu'] = $commentaire->getContenu();
                        $result[$key]['avatar'] = "data:image/jpeg;base64," . $user->getAvatar();
                    }
                }
                $data['commentaires'] = $result;
                return $this->json($data, 200);
            }
    }

    /**
     * @Route( "api/commentaire/restaurer/{id}", name="restaurerCommentaire", methods={"PUT"} )
     */
    public function restaurer($id)
    {
        if ($this->isGranted('ROLE_Admin')) {
            $commentaire = $this->commentaireRepository->findOneBy(['id' => $id]);
            $commentaire->setIsSignaled(false);
            $commentaire->setMotifSignal(null);
            $this->manager->persist($commentaire);
            $this->manager->flush();
            return $this->json('Vous ne pouvez pas faire cette action désolé !',200);
        }else {
            return $this->json('Vous ne pouvez pas faire cette action désolé !',500);
        }
    }

    /**
     * @Route( "api/commentaire/modele/{id}", name="modeleCommentaire", methods={"GET"} )
     */
    public function getModelComments($id)
    {
        $data =[];
        $model = $this->modelRepository->findOneBy(['id' => $id]);
        $commentaires = $model->getCommentaires();
        foreach ($commentaires as $key =>  $commentaire)
        {
            if($commentaire->getIsSignaled() == false){
                $userId =$commentaire->getUser()->getId();
                $user = $this->userRepository->findOneBy(['id' => $userId]);

                $data[$key]['id']= $commentaire->getId();
                $data[$key]['contenu']= $commentaire->getContenu();
                $data[$key]['avatar']= "data:image/jpeg;base64,".$user->getAvatar();
            }
            
        }
        return $this->json($data,200);
    }
}
