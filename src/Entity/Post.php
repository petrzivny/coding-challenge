<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\IdTrait;
use App\Service\TextService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/** @ORM\Entity */
class Post implements \JsonSerializable
{
    use IdTrait;

    /**
     * Number of posts in one page, the rest is paginated.
     * @var int
     */
    public const POSTS_PER_PAGE = 2;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(max=150)
     * TODO any other Asserts here?
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * TODO any other Asserts here?
     */
    private $text;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $enabled = true;

    /**
     * @var int
     * @ORM\Column(type="bigint")
     */
    private $views = 0;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag")
     */
    private $tags;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /** @throws \Exception */
    public function __construct()
    {
        if (null == $this->id) {
            $this->generateId();
        }

        if (null == $this->createdAt) {
            $this->createdAt = new \DateTimeImmutable();
        }

        $this->tags = new ArrayCollection();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getViews(): ?int
    {
        return (int)$this->views;
    }

    public function increaseVisitCountByOne(): self
    {
        $this->views++;

        return $this;
    }

    /** @return Collection|Tag[] */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        foreach ($this->tags as $tag) {
            $tags[] = $tag->getName();
        }

        return [
            'date' => $this->createdAt,
            'title' => $this->title,
            'text' => $this->text,
            'url' => $this->url,
            'tags' => $tags ?? [],
        ];
    }

    /** @throws \Exception */
    public function generateUrl(): string
    {
        if (!$this->title) {
            throw new \Exception('cannot_generate_url_title_is_not_set');
        }
        $url = TextService::removeAccents($this->title);
        $url = mb_strtolower($url);
        $url = preg_replace('/[^a-z0-9_-]/is', '-', $url);
        $url = $url . '-' . mb_substr($this->id, 0, 5);

        return $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
