<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * * @UniqueEntity(
 *     fields={"email"},
 *     message="Email déjà utilisé."
 * )
 */

class User
{

  public function __construct($email){
    $this->email = $email;
    $this->create_at = new \Datetime();
    $this->last_request = new \Datetime();
    $this->nickname = "";
    $this->genKey();
    $this->histories = new ArrayCollection();
  }

   public function updatePoints(int $points): self
   {
       $this->points += $points;
       return $this;
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
    * @Serializer\Groups({"listUser", "nickname"})
     */

    private $email;


    /**
     * @ORM\Column(type="string", length=255)
      * @Serializer\Groups({"nickname"})
     */

     private $nickname;

    /**
    * @ORM\Column(type="string", length=255)
    */
    private $apikey;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"listUser", "nickname"})
     */
    private $points = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"listUser"})
     */
    private $last_request;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\History", mappedBy="user")
     */
    private $histories;


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


    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setUser($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->contains($history)) {
            $this->histories->removeElement($history);
            // set the owning side to null (unless already changed)
            if ($history->getUser() === $this) {
                $history->setUser(null);
            }
        }

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }
}
