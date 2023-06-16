<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;

/**
 * @ORM\Entity(repositoryClass=UrlRepository::class)
 */
class Url
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $url;

    /**
     * @ORM\Column(name="created_date", type="datetime_immutable")
     */
    protected $createdDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedDate(string $datetime)
    {
        return $this->createdDate;
    }

    public function setCreatedDate(string $datetime)
    {
        $this->createdDate = new DateTimeImmutable($datetime);
    }
}
