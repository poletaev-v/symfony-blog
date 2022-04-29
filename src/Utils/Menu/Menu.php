<?php

namespace App\Utils\Menu;

use App\Repository\CategoryRepository;
use App\Template\MenuTemplate;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Cache\CacheItem;

class Menu
{
    private const CACHE_TTL = 60 * 60 * 24 * 30;
    private const CACHE_KEY = 'menu.category_list';
    /**
     * @var CategoryTree[] $categoryList
     */
    public array $categoryList = [];
    private CategoryRepository $categoryRepo;
    private CacheInterface $cache;

    public function __construct(CategoryRepository $categoryRepo, CacheInterface $cache)
    {
        $this->categoryRepo = $categoryRepo;
        $this->cache = $cache;
    }

    public function load(): self
    {
        /**
         * @var CacheItem $cacheCategoryList;
         */
        $cacheCategoryList = $this->cache->getItem(self::CACHE_KEY);
        if ($cacheCategoryList->isHit()) {
            $this->categoryList = $cacheCategoryList->get();
            return $this;
        }

        $categories = $this->categoryRepo->findAll();
        $lists = [];
        // assemble category roots and children
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

                if ($rootCategory) {
                    $rootCategory->insert($list->current());
                }
            }
        }

        $cacheCategoryList->set($this->categoryList);
        $cacheCategoryList->expiresAfter(self::CACHE_TTL);
        $this->cache->save($cacheCategoryList);

        return $this;
    }

    public function getContent(): string
    {
        return MenuTemplate::getContent($this->categoryList);
    }
}