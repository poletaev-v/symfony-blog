<?php

namespace App\Utils\Menu;

use App\Entity\Category;

class CategoryTree
{
    private ?Category $value;
    private Collection $children;

    public function __construct(Category $category)
    {
        $this->value = $category;
        $this->children = new Collection();
    }

    public function insert(Category $category): void
    {
        $tree = new CategoryTree($category);
        if (is_null($this->search($this, $category->getId()))) {
            $child = $this->search($this, $category->getParentCategory()->getId());
            if (is_null($child)) {
                $this->children->push($tree);
            } else {
                $child->getCollection()->push($tree);
            }
        }
    }

    private function search(CategoryTree $tree, int $id): ?CategoryTree
    {
        if ($tree->value->getId() === $id) {
            return $tree;
        }

        $foundedChild = null;
        /**
         * @var CategoryTree $child
         */
        foreach ($tree->getChildren() as $child) {
            if (!is_null($tempChild = $child->search($child, $id))) {
                $foundedChild = $tempChild;
            }
        }
        return $foundedChild;
    }

    public function getCollection(): Collection
    {
        return $this->children;
    }

    public function getChildren(): array
    {
        return $this->children->getCollection();
    }

    public function isEmpty(): bool
    {
        return $this->getCollection()->isEmpty();
    }

    public function getValue(): ?Category
    {
        return $this->value;
    }
}