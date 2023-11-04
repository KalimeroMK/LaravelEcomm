<?php

namespace Modules\Newsletter\Service;

use Modules\Newsletter\Repository\NewsletterRepository;

class NewsletterService
{
    public NewsletterRepository $newsletter_repository;

    public function __construct(NewsletterRepository $newsletter_repository)
    {
        $this->newsletter_repository = $newsletter_repository;
    }

    public function store($data)
    {
        return $this->newsletter_repository->create($data);
    }

    public function edit($id)
    {
        return $this->newsletter_repository->findById($id);
    }

    public function show($id)
    {
        return $this->newsletter_repository->findById($id);
    }

    public function update($id, $data)
    {
        return $this->newsletter_repository->update($id, $data);
    }

    public function destroy($id): void
    {
        $this->newsletter_repository->delete($id);
    }

    public function getAll()
    {
        return $this->newsletter_repository->findAll();
    }
}
