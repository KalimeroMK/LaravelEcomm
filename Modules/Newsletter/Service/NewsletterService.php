<?php

namespace Modules\Newsletter\Service;

use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
use Modules\Banner\Models\Banner;
use Modules\Core\Service\CoreService;
use Modules\Newsletter\Repository\NewsletterRepository;

class NewsletterService extends CoreService
{

    public NewsletterRepository $newsletter_repository;

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
            return $this->newsletter_repository->create($data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
            return $this->newsletter_repository->findById($id);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
            return $this->newsletter_repository->findById($id);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
            return $this->newsletter_repository->update($id, $data);
    }

    /**
     * @param $id
     *
     * @return string|void
     */

    public function destroy($id)
    {
            $this->newsletter_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
            return $this->newsletter_repository->findAll();
    }
}
