<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation\Type;

/**
 * Service
 *
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 */
class Category
{

    public function __construct() {
        $this->children = [];
    }

    /**
     * @var int
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_visible", type="boolean", nullable=true)
     */
    private $is_visible;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     */
    protected $children;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name) : Category
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get Slug
     *
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * Set name
     *
     * @param string $slug
     *
     * @return Category
     */
    public function setSlug($slug) : Category
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get Slug
     *
     * @return bool
     */
    public function getIsVisible() : bool
    {
        return $this->is_visible;
    }

    /**
     * Set name
     *
     * @param string|bool $is_visible
     *
     * @return Category
     */
    public function setIsVisible($is_visible) : Category
    {
        $this->is_visible = $is_visible;

        return $this;
    }

    /**
     * get parent
     * @return Category
     */
    public function getParent() : Category
    {
        return $this->parent;
    }

    /**
     * getChildren
     * @return PersistentCollection
     */
    public function getChildren() : PersistentCollection
    {
        return $this->children;
    }

    /**
     * add Child to current Entity
     * @param Category $child
     * @return Category
     */
    public function addChild(Category $child) : Category
    {
        $this->children[] = $child;
        $child->setParent($this);
        return $child;
    }

    /**
     * set Parent for current Entity
     * @param Category $parent
     * @return Category
     */
    public function setParent(Category $parent) : Category
    {
        $this->parent = $parent;
        return $this->parent;
    }

}
