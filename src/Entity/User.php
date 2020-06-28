<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
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
	private $firstName;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $lastName;

	/**
	 * @ORM\ManyToOne(targetEntity=Workshop::class, inversedBy="users")
	 * @ORM\JoinColumn(nullable=true)
	 */
	private $workshop;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $mask;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $login;

	/**
	 * @ORM\OneToMany(targetEntity=AccessToken::class, mappedBy="owner", orphanRemoval=true, fetch="EAGER")
	 */
	private $accessTokens;

	public function __construct()
	{
		$this->accessTokens = new ArrayCollection();
	}


	public function getId(): ?int
	{
		return $this->id;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	public function setFirstName(string $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	public function setLastName(string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function getWorkshop(): ?Workshop
	{
		return $this->workshop;
	}

	public function setWorkshop(?Workshop $workshop): self
	{
		$this->workshop = $workshop;

		return $this;
	}


	public function getMask(): ?int
	{
		return $this->mask;
	}

	public function setMask(int $mask): self
	{
		$this->mask = $mask;

		return $this;
	}

	public static function loadValidatorMetadata(ClassMetadata $metadata)
	{
		$metadata->addPropertyConstraints('firstName', [
			new NotBlank(),
			new Length(['min' => 3, 'allowEmptyString' => false]),
		]);
		$metadata->addPropertyConstraints('lastName', [
			new NotBlank(),
			new Length(['min' => 3, 'allowEmptyString' => false]),
		]);
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

	public function export(): array
	{
		return [
			'id' => $this->id,
			'first_name' => $this->firstName,
			'last_name' => $this->lastName,
			'mask' => $this->mask,
			'workshop_id' => $this->workshop ? $this->workshop->getId() : null,
		];
	}

	public function getLogin(): ?string
	{
		return $this->login;
	}

	public function setLogin(string $login): self
	{
		$this->login = $login;

		return $this;
	}

	/**
	 * @return Collection|AccessToken[]
	 */
	public function getAccessTokens(): Collection
	{
		return $this->accessTokens;
	}

	public function addAccessToken(AccessToken $accessToken): self
	{
		if (!$this->accessTokens->contains($accessToken)) {
			$this->accessTokens[] = $accessToken;
			$accessToken->setOwner($this);
		}

		return $this;
	}

	public function removeAccessToken(AccessToken $accessToken): self
	{
		if ($this->accessTokens->contains($accessToken)) {
			$this->accessTokens->removeElement($accessToken);
			// set the owning side to null (unless already changed)
			if ($accessToken->getOwner() === $this) {
				$accessToken->setOwner(null);
			}
		}

		return $this;
	}
}
