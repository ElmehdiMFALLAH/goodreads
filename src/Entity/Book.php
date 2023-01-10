<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'book:item']),
        new GetCollection(normalizationContext: ['groups' => 'book:list']),
        new Post(normalizationContext: ['groups' => 'book:add']),
    ]
)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['book:item', 'book:list', 'book:add'])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['book:item', 'book:list', 'book:add'])]
    private ?int $pages = null;

    #[ORM\Column(length: 50)]
    #[Groups(['book:item', 'book:list', 'book:add'])]
    private ?string $edition = null;

    #[ORM\OneToMany(mappedBy: 'book', targetEntity: Read::class)]
    private Collection $bkReads;

    public function __construct()
    {
        $this->bkReads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPages(): ?int
    {
        return $this->pages;
    }

    public function setPages(int $pages): self
    {
        $this->pages = $pages;

        return $this;
    }

    public function getEdition(): ?string
    {
        return $this->edition;
    }

    public function setEdition(string $edition): self
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * @return Collection<int, Read>
     */
    public function getBkReads(): Collection
    {
        return $this->bkReads;
    }

    public function addBkRead(Read $bkRead): self
    {
        if (!$this->bkReads->contains($bkRead)) {
            $this->bkReads->add($bkRead);
            $bkRead->setBook($this);
        }

        return $this;
    }

    public function removeBkRead(Read $bkRead): self
    {
        if ($this->bkReads->removeElement($bkRead)) {
            // set the owning side to null (unless already changed)
            if ($bkRead->getBook() === $this) {
                $bkRead->setBook(null);
            }
        }

        return $this;
    }
}
