<?php

namespace App\Services\Site;

use App\Models\Site;
use App\Services\BaseService;
use App\Services\ResultsService;

class SiteService extends BaseService
{
    private ResultsService $resultsService;

    private Site $site;

    public function __construct(ResultsService $resultsService)
    {
        parent::__construct();
        $this->resultsService = $resultsService;
    }

    public function findBy(string $key, string|int $value)
    {
        return Site::where($key, $value)->get();
    }

    public function findOneBy(string $key, string|int $value)
    {
        return Site::where($key, $value)->first();
    }

    public function createSite(array $data)
    {
        $this->site = new Site($data);
        if (!$this->site->save()) {
            $this->resultsService->addError('Error creating site', $data);
            return false;
        }
        return true;
    }

    public function updateSite(Site $site, array $data)
    {
        $this->site = $site;
        if (!$this->site->update($data)) {
            $this->resultsService->addError('Error updating site', $data);
            return false;
        }
        return true;
    }

    public function deleteSite(Site $site)
    {
        $this->site = $site;
        if (!$this->site->delete()) {
            $this->resultsService->addError('Error deleting site');
            return false;
        }
        return true;
    }

    /**
     * @return ResultsService
     */
    public function getResultsService(): ResultsService
    {
        return $this->resultsService;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }
}
