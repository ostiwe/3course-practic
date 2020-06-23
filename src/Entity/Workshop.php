<?php

namespace App\Entity;

use App\Repository\WorkshopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=WorkshopRepository::class)
 */
class Workshop
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $name;

	/**
	 * @ORM\OneToMany(targetEntity=User::class, mappedBy="workshop")
	 */
	private $users;

	/**
	 * @ORM\OneToOne(targetEntity=User::class)
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $director;

    /**
     * @ORM\OneToMany(targetEntity=Auto::class, mappedBy="workshop")
     */
    private $autos;

	public function __construct()
               	{
               		$this->users = new ArrayCollection();
                 $this->autos = new ArrayCollection();
               	}

	public function getId(): ?int
               	{
               		return $this->id;
               	}

	public function getName(): ?string
               	{
               		return $this->name;
               	}

	public function setName(string $name): self
               	{
               		$this->name = $name;
               
               		return $this;
               	}

	/**
	 * @return Collection|User[]
	 */
	public function getUsers(): Collection
               	{
               		return $this->users;
               	}

	public function addUser(User $user): self
               	{
               		if (!$this->users->contains($user)) {
               			$this->users[] = $user;
               			$user->setWorkshop($this);
               		}
               
               		return $this;
               	}

	public function removeUser(User $user): self
               	{
               		if ($this->users->contains($user)) {
               			$this->users->removeElement($user);
               			// set the owning side to null (unless already changed)
               			if ($user->getWorkshop() === $this) {
               				$user->setWorkshop(null);
               			}
               		}
               
               		return $this;
               	}

	public function getDirector(): ?User
               	{
               		return $this->director;
               	}

	public function setDirector(User $director): self
               	{
               		$this->director = $director;
               
               		return $this;
               	}

	public static function loadValidatorMetadata(ClassMetadata $metadata)
               	{
               		$metadata->addPropertyConstraints('name', [
               			new NotBlank(),
               			new Length(['min' => 3, 'allowEmptyString' => false]),
               		]);
               	}

    /**
     * @return Collection|Auto[]
     */
    public function getAutos(): Collection
    {
        return $this->autos;
    }

    public function addAuto(Auto $auto): self
    {
        if (!$this->autos->contains($auto)) {
            $this->autos[] = $auto;
            $auto->setWorkshop($this);
        }

        return $this;
    }

    public function removeAuto(Auto $auto): self
    {
        if ($this->autos->contains($auto)) {
            $this->autos->removeElement($auto);
            // set the owning side to null (unless already changed)
            if ($auto->getWorkshop() === $this) {
                $auto->setWorkshop(null);
            }
        }

        return $this;
    }
}
