<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

class HttpMethodSorter
{
    private const METHOD_KEY = "method";

    private Request $request;
    private array $availableMethods = ["desc", "asc"];
    private string $defaultMethodSort = "desc";

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getMethod(): string
    {
        $sortMethod = $this->request->get(self::METHOD_KEY);
        if (is_null($sortMethod)) {
            return $this->defaultMethodSort;
        }

        if (!in_array(strtolower($sortMethod), $this->availableMethods)) {
            $sortMethod = $this->defaultMethodSort;
        }
        return $sortMethod;
    }

    public function getAvailableMethods(): array
    {
        return $this->availableMethods;
    }

    public function setAvailableMethods(array $availableMethods): void
    {
        $this->availableMethods = $availableMethods;
    }

    public function getDefaultMethodSort(): string
    {
        return $this->defaultMethodSort;
    }

    public function setDefaultMethodSort(string $defaultMethodSort): void
    {
        $this->defaultMethodSort = $defaultMethodSort;
    }
}