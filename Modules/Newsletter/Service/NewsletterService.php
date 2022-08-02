<?php

namespace Modules\Newsletter\Service;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
use Modules\Banner\Models\Banner;
use Modules\Core\Service\CoreService;
use Modules\Newsletter\Repository\NewsletterRepository;

class NewsletterService extends CoreService
{
    
    private NewsletterRepository $newsletter_repository;
    
    public function __construct(NewsletterRepository $newsletter_repository)
    {
        $this->newsletter_repository = $newsletter_repository;
    }
    
    /**
     * @param $data
     *
     * @return Collection|_IH_Banner_C|mixed|Banner|Banner[]
     */
    public function store($data): mixed
    {
        try {
            return $this->newsletter_repository->create($data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
        try {
            return $this->newsletter_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
        try {
            return $this->newsletter_repository->update($id, $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $banner
     *
     * @return string|void
     */
    
    public function destroy($banner)
    {
        try {
            $this->newsletter_repository->delete($banner->id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        try {
            return $this->newsletter_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}