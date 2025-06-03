<?php

namespace App\Repositories;

use App\Models\PageBlock;
use App\Models\Site;

class PageBlockRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(PageBlock::class);
    }

    public function getModel(): PageBlock
    {
        return parent::getModel();
    }

    public function findByParams(string $sort, string  $order, ?int $count = null)
    {
        return $this->findAllWithParams($sort, $order, $count);
    }

    public function findByQuery($query)
    {
        return $this->findAll();
    }

    public function getSitePageBlocks(Site $site)
    {
        $this->setQuery(
            $site->pages()
        );
        $this->setWith([
            'blocks' => function ($query) {
                $query->orderBy('order');
            }
        ]);
        $this->setOrderByColumn('created_at');
        $this->setOrderByDir('desc');
        return $this->findMany();
    }

}
