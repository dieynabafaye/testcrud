<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type_client", type="string")
 * @ORM\DiscriminatorMap({"admin"="Admin","client"="Client","tailleur"="Tailleur","user"="User"})
 * @ApiResource(
 *     itemOperations={
 *      "GET":{
 *          "method":"GET",
 *          "path":"/users/{id}",
 *          "normalization_context"={"groups":"user:read"},
 *           "security"="is_granted('ROLE_Client') or is_granted('ROLE_Tailleur') or is_granted('ROLE_Admin')",
 *           "access_control_message"="Vous n'avez pas access à cette Ressource",
 *        },
 *      "DELETE":{
 *          "method":"DELETE",
 *          "path":"/users/{id}",
 *           "security"=" is_granted('ROLE_Admin') or is_granted('ROLE_Client') or is_granted('ROLE_Tailleur')",
 *           "access_control_message"="Vous n'avez pas access à cette Ressource",
 *       },
 *      "PUT":{
 *           "route_name"="updateUser",
 *           "method":"PUT",
 *           "path":"/users/{id}",
 *           "security"="is_granted('ROLE_Client') or is_granted('ROLE_Tailleur') or object.owner == user",
 *           "access_control_message"="Vous ne pouvez pas modifier un utilisateur",
 *            "deserialize"= false
 *      }
 *     },
 *     collectionOperations={
 *      "GET":{
 *           "method":"GET",
 *           "path":"/users",
 *            "normalization_context"={"groups":"user:read"},
 *            "security"="is_granted('ROLE_Admin')",
 *            "security_message"="Vous n'avez pas access à cette Ressource"
 *     },
 *      "POST":{
 *            "route_name"="addUser",
 *           "method":"POST",
 *           "path":"/users"
 *      }
 *
 *     }
 * )
 *
 *
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isArchivate;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     */
    private $profil;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $debutAbonnement;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $finAbonnement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="users")
     */
    private $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->isArchivate = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->telephone;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->telephone;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.$this->profil->getLibelle();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getIsArchivate(): ?bool
    {
        return $this->isArchivate;
    }

    public function setIsArchivate(bool $isArchivate): self
    {
        $this->isArchivate = $isArchivate;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getDebutAbonnement(): ?\DateTimeInterface
    {
        return $this->debutAbonnement;
    }

    public function setDebutAbonnement(?\DateTimeInterface $debutAbonnement): self
    {
        $this->debutAbonnement = $debutAbonnement;

        return $this;
    }

    public function getFinAbonnement(): ?\DateTimeInterface
    {
        return $this->finAbonnement;
    }

    public function setFinAbonnement(?\DateTimeInterface $finAbonnement): self
    {
        $this->finAbonnement = $finAbonnement;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setUsers($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUsers() === $this) {
                $commentaire->setUsers(null);
            }
        }

        return $this;
    }
}
