<?php

namespace App\Traits;

use App\Models\Site;
use App\Repositories\SiteRepository;

trait SiteTrait
{
    
    public Site $site;
    public SiteRepository $siteRepository;

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site): self
    {
        $this->site = $site;
        return $this;
    }

    public function getSiteRepository(): SiteRepository
    {
        return $this->siteRepository;
    }

    public function setSiteRepository(SiteRepository $siteRepository): void
    {
        $this->siteRepository = $siteRepository;
    }

}