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

    /**
     * Store a new attribute.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->newsletter_repository->create($data);
    }

    /**
     * Show details of an attribute.
     *
     * @param  int  $id  The attribute ID.
     * @return mixed
     */
    public function edit(int $id): mixed
    {
        return $this->newsletter_repository->findById($id);
    }

    /**
     * Show details of an attribute.
     *
     * @param  int  $id  The attribute ID.
     * @return mixed
     */
    public function show(int $id): mixed
    {
        return $this->newsletter_repository->findById($id);
    }

    /**
     * Update the specified coupon.
     * @param  int  $id  The ID of the coupon to update.
     * @param  array<string, mixed>  $data  Data to update the coupon.
     * @return bool Result of the update operation.
     */
    public function update(int $id, array $data): mixed
    {
        return $this->newsletter_repository->update($id, $data);
    }

    /**
     * Delete an attribute.
     *
     * @param  int  $id  The attribute ID.
     */
    public function destroy(int $id): void
    {
        $this->newsletter_repository->delete($id);
    }

    /**
     * Get all attributes.
     *
     * @return object
     */
    public function getAll(): object
    {
        return $this->newsletter_repository->findAll();
    }
}
