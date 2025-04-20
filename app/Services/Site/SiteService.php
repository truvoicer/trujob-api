<?php

namespace App\Services\Site;

use App\Enums\Auth\ApiAbility;
use App\Enums\Auth\ApiTokenExpiry;
use App\Models\Site;
use App\Services\Admin\AuthService;
use App\Services\BaseService;
use App\Services\ResultsService;
use App\Traits\ApiTokenTrait;

class SiteService extends BaseService
{
    use ApiTokenTrait;

    private ResultsService $resultsService;

    public Site $site;

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

    public function createToken(Site $site, ?ApiTokenExpiry $expiry = ApiTokenExpiry::NEVER)
    {

        if (empty($expiry)) {
            $expiry = ApiTokenExpiry::NEVER;
        }
        
        if ($expiry !== ApiTokenExpiry::NEVER) {
            $expiryDate = new \DateTime($expiry->value);
        } else {
            $expiryDate = null;
        }
        
        $siteAbilityData = AuthService::getApiAbilityData(ApiAbility::SITE->value);
        if (!$siteAbilityData) {
            throw new \Exception('Site ability not found');
        }
        return $site->createToken(ApiAbility::SITE->value, [$siteAbilityData['ability']], $expiryDate);
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

}
