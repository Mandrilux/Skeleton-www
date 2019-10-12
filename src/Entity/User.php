<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * * @UniqueEntity(
 *     fields={"email"},
 *     message="Email déjà utilisé."
 * )
 */

class User
{

  public function init()
   {
       /*$this->name = $name;*/
       $this->create_at = new \Datetime();
       $this->last_request = new \Datetime();
       $this->genKey();
   }

   public function genKey(){
     $this->apikey = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));
   }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     * @Assert\Email(message="Mauvais format d'email")
     * @Assert\Length(min="14", max="150", minMessage="Mauvais format d'email", maxMessage="Mauvais format d'email")
     */

    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Le mot de passe doit être renseigné")
     * @Assert\Length(min="6", max="150", minMessage="Le mot de passe doit contenir au moins 6 caractères", maxMessage="Le mot de passe doit contenir au maximum 150 caractères")
     */

    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $apikey;

    /**
     * @ORM\Column(type="integer")
     */
    private $points = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $last_request;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getLastRequest(): ?\DateTimeInterface
    {
        return $this->last_request;
    }

    public function setLastRequest(\DateTimeInterface $last_request): self
    {
        $this->last_request = $last_request;

        return $this;
    }

    public function getApikey(): ?string
    {
        return $this->apikey;
    }

    public function setApikey(string $apikey): self
    {
        $this->apikey = $apikey;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
