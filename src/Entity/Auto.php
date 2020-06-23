<?php

namespace App\Entity;

use App\Repository\AutoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AutoRepository::class)
 */
class Auto
{
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity=AutoModel::class, inversedBy="autos")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $model;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="string", length=255,unique=true)
	 */
	private $serialNumber;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $power;

	/**
	 * @ORM\ManyToOne(targetEntity=Workshop::class, inversedBy="autos")
	 */
	private $workshop;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getModel(): ?AutoModel
	{
		return $this->model;
	}

	public function setModel(?AutoModel $model): self
	{
		$this->model = $model;

		return $this;
	}

	public function getCreatedAt(): ?int
	{
		return $this->createdAt;
	}

	public function setCreatedAt(int $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getSerialNumber(): ?string
	{
		return $this->serialNumber;
	}

	public function setSerialNumber(string $serialNumber): self
	{
		$this->serialNumber = $serialNumber;

		return $this;
	}

	public function getPower(): ?int
	{
		return $this->power;
	}

	public function setPower(int $power): self
	{
		$this->power = $power;

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

	public function export(): array
	{
		return [
			'id' => $this->id,
			'model_id' => $this->model->getId(),
			'workshop_id' => $this->workshop->getId(),
			'created_at' => $this->createdAt,
			'serial_number' => $this->serialNumber,
			'power' => $this->power,
		];
	}
}
