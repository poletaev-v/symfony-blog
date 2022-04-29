<?php

namespace App\Utils\Menu;

class Collection
{
    /**
     * @var CategoryTree[] $collection
     */
    private array $collection = [];

    public function push(CategoryTree $item): self
    {
        $this->collection[$item->getValue()->getId()] = $item;
        return $this;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function isEmpty(): bool
    {
        return empty($this->collection);
    }
}