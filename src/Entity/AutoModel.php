<?php

namespace App\Entity;

use App\Repository\AutoModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AutoModelRepository::class)
 */
class AutoModel
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
	 * @ORM\OneToMany(targetEntity=Auto::class, mappedBy="model")
	 */
	private $autos;

	public function __construct()
	{
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
			$auto->setModel($this);
		}

		return $this;
	}

	public function removeAuto(Auto $auto): self
	{
		if ($this->autos->contains($auto)) {
			$this->autos->removeElement($auto);
			// set the owning side to null (unless already changed)
			if ($auto->getModel() === $this) {
				$auto->setModel(null);
			}
		}

		return $this;
	}

	public function export(): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}
}
