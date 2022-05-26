<?php

namespace App\Entity;

use App\Entity\Exception\TooOldPersonException;
use App\Repository\PersonneRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne
{
    #[
        ORM\Id,
        ORM\Column(type: 'uuid', unique: true)
    ]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private string $prenom;

    #[ORM\Column(type: 'date')]
    private DateTimeInterface $dateDeNaissance;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateDeNaissance(): DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    /**
     * @throws TooOldPersonException
     */
    public function setDateDeNaissance(DateTimeInterface $dateDeNaissance): self
    {
        if ($dateDeNaissance->diff(new DateTime())->y > 150) {
            throw new TooOldPersonException('La personne doit être née depuis moins de 150 ans');
        }

        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }
}
