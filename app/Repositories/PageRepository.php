<?php

namespace App\Repositories;

use App\Models\Page;
use App\Models\Site;

class PageRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(Page::class);
    }

    public function getModel(): Page
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

    public function getSitePages(Site $site)
    {
        $this->setQuery(
            $site->pages()
        );
        $this->setWith([
            'blocks' => function ($query) {
                $query->orderBy('order');
            }
        ]);
        $this->setSortField('created_at');
        $this->setOrderDir('desc');
        return $this->findMany();
    }

}
