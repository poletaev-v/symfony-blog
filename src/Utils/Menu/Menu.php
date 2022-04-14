<?php

namespace App\Utils\Menu;

use App\Repository\CategoryRepository;
use App\Template\MenuTemplate;

class Menu
{
    /**
     * @var CategoryTree[] $categoryList
     */
    public array $categoryList = [];
    private CategoryRepository $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function load(): self
    {
        $categories = $this->categoryRepo->findAll();
        $lists = [];
        foreach ($categories as $category) {
            if ($category->getParentCategory() === null) {
                $this->categoryList[$category->getId()] = new CategoryTree($category);
                continue;
            }

            $list = new \SplDoublyLinkedList();
            while (true) {
                $list->push($category);
                if (is_null($category->getParentCategory())) {
                    break;
                }
                $category = $category->getParentCategory();
            }
            $lists[] = $list;
        }

        foreach ($lists as $list) {
            $rootCategory = null;
            $list->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO);
            for ($list->rewind(); $list->valid(); $list->next()) {
                if (array_key_exists($list->current()->getId(), $this->categoryList)) {
                    $rootCategory = $this->categoryList[$list->current()->getId()];
                    continue;
                }

                $rootCategory->insert($list->current());
            }
        }

        return $this;
    }

    public function getView(): string
    {
        return MenuTemplate::render($this->categoryList);
    }
}