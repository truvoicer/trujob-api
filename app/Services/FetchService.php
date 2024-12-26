<?php

namespace App\Services;

trait FetchService
{
    private array $select = [];

    private array $where = [];
    private string $groupBy;
    private string $orderBy;
    private ?int $limit = 10;
    private ?int $offset = null;
    private string $orderDir = 'desc';

    private ?bool $pagination = false;

    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @param array $select
     */
    public function setSelect(array $select): void
    {
        $this->select = $select;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @param array $where
     */
    public function setWhere(array $where): void
    {
        $this->where = $where;
    }

    /**
     * @return string
     */
    public function getGroupBy(): string
    {
        return $this->groupBy;
    }

    /**
     * @param string $groupBy
     */
    public function setGroupBy(string $groupBy): void
    {
        $this->groupBy = $groupBy;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     */
    public function setOrderBy(string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     */
    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getOrderDir(): string
    {
        return $this->orderDir;
    }

    /**
     * @param string $orderDir
     */
    public function setOrderDir(string $orderDir): void
    {
        $this->orderDir = $orderDir;
    }

    /**
     * @return bool|null
     */
    public function getPagination(): ?bool
    {
        return $this->pagination;
    }

    /**
     * @param bool|null $pagination
     */
    public function setPagination(?bool $pagination): void
    {
        $this->pagination = $pagination;
    }
}
