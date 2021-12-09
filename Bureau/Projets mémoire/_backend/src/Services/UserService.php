<?php


namespace App\Services;

use App\Entity\Admin;
use App\Entity\Client;
use App\Entity\Tailleur;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserService
{
    private UserPasswordHasherInterface $encoder;
    private SerializerInterface $serializer;
    private ProfilRepository $profilRepository;

    /**
     * AddUserService constructor.
     */
    public function __construct(SerializerInterface $serializer, UserPasswordHasherInterface $encoder,ProfilRepository $profilRepository)
    {
        $this->encoder =$encoder;
        $this->serializer = $serializer;
        $this->profilRepository = $profilRepository;
    }


    public function NewUser(Request $request)
    {

        $userReq = $request->request->all();

        $profil = $userReq['type'];
        $uploadedFile = $request->files->get('avatar');
        if($uploadedFile){
            $file = $uploadedFile->getRealPath();
            $userReq['avatar']= fopen($file,'r+');
        }

        if($profil == "Tailleur"){
            $user = Tailleur::class;
        }elseif ($profil == "Client"){
            $user = Client::class;
        }elseif ($profil == "Admin"){
            $user = Admin::class;
        }

        $newUser = $this->serializer->denormalize($userReq, $user,true);
        $newUser->setPrenom(ucfirst($userReq['prenom']));
        $newUser->setNom(strtoupper($userReq['nom']));

        $newUser->setProfil($this->profilRepository->findOneBy(['libelle'=>$profil]));
        $newUser->setIsArchivate(false);
        $newUser->setPassword($this->encoder->hashPassword($newUser,$userReq['password']));

        return $newUser;
    }

    /**
     * @param Request $request
     * @param string|null $fileName
     * @return array
     */
    public function GestionImage(Request $request, string $fileName = null): array
    {
        $raw = $request->getContent();
        $delimiteur = "multipart/form-data; boundary=";
        $boundary = "--" . explode($delimiteur, $request->headers->get("content-type"))[1];
        $elements = str_replace([$boundary, 'Content-Disposition: form-data;', "name="], "", $raw);
        $elementsTab = explode("\r\n\r\n", $elements);
        $data = [];
        for ($i = 0; isset($elementsTab[$i + 1]); $i += 2) {
            $key = str_replace(["\r\n", ' "', '"'], '', $elementsTab[$i]);
            if (strchr($key, $fileName)) {
                $stream = fopen('php://memory', 'r+');
                fwrite($stream, $elementsTab[$i + 1]);
                rewind($stream);
                $data[$fileName] = $stream;
            } else {
                $val = $elementsTab[$i + 1];
                $val = str_replace(["\r\n", "--"],'',$elementsTab[$i+1]);
                $data[$key] = $val;
            }
        }
        /*if (isset($data["profil"])){
            $newProfil = $this->profilRepository->findOneBy(['libelle' => $data["profil"]]);
            $data["profil"] = $newProfil;
        }*/

        //on retourne les nouvelles donnees avec l'image modifier et traiter
        return $data;
    }
}
