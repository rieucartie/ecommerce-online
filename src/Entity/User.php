<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert; 
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; 
/** 
* @ORM\Entity(repositoryClass="App\Repository\UserRepository") 
* @UniqueEntity( 
* fields={"email"}, 
* message="L'email que vous avez tapé est déjà utilisé !"
* ) 
*/ 

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;
    
       /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;
     /**
     * @ORM\Column(type="string", length=180, unique=true)
     */

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
    * @ORM\Column(type="string", length=255)
    * @Assert\Length(
    * min = 8,
    * minMessage = "Votre mot de passe doit comporter au minimum {{ limit }} caractères")
    */
    private $password;
    /**
    * @Assert\EqualTo(propertyPath = "confirm_password",
    * message="Vous n'avez pas saisi le même mot de passe !" )
    */
    private $confirm_password;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="user")
     */
    private $commandes;

    /**
     * @ORM\OneToMany(targetEntity=UtilisateurAdresse::class, mappedBy="user")
     */
    private $userAdresse;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="user", orphanRemoval=true)
     */
    private $questions;



    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->userAdresse = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    
    public function getConfirmPassword() 
    { 
        return $this->confirm_password;
    } 
    
    
    public function setConfirmPassword($confirm_password) 
    { 
        $this->confirm_password = $confirm_password;
        return $this;
    } 


    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

     /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UtilisateurAdresse[]
     */
    public function getUserAdresse(): Collection
    {
        return $this->userAdresse;
    }

    public function addUserAdresse(UtilisateurAdresse $userAdresse): self
    {
        if (!$this->userAdresse->contains($userAdresse)) {
            $this->userAdresse[] = $userAdresse;
            $userAdresse->setUser($this);
        }

        return $this;
    }

    public function removeUserAdresse(UtilisateurAdresse $userAdresse): self
    {
        if ($this->userAdresse->contains($userAdresse)) {
            $this->userAdresse->removeElement($userAdresse);
            // set the owning side to null (unless already changed)
            if ($userAdresse->getUser() === $this) {
                $userAdresse->setUser(null);
            }
        }

        return $this;
    }
    
       public function __toString()
    {
        

        return $this->email ;
    }

       /**
        * @return Collection|Question[]
        */
       public function getQuestions(): Collection
       {
           return $this->questions;
       }

       public function addQuestion(Question $question): self
       {
           if (!$this->questions->contains($question)) {
               $this->questions[] = $question;
               $question->setUser($this);
           }

           return $this;
       }

       public function removeQuestion(Question $question): self
       {
           if ($this->questions->removeElement($question)) {
               // set the owning side to null (unless already changed)
               if ($question->getUser() === $this) {
                   $question->setUser(null);
               }
           }

           return $this;
       }


    public function getUserIdentifier(): string
    {
        return '';
    }
}
