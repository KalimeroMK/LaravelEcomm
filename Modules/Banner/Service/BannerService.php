<?php

namespace Modules\Banner\Service;

use Illuminate\Database\Eloquent\Model;
use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;

class BannerService extends CoreService
{
    use ImageUpload;

    public BannerRepository $banner_repository;

    public function __construct(BannerRepository $banner_repository)
    {
        parent::__construct($banner_repository);
    }


    /**
     * Update an existing attribute.
     *
     * @param  int  $id  The banner ID to update.
     * @param  array<string, mixed>  $data  The data for updating the attribute.
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        if (!empty($data['photo'])) {
            return $this->banner_repository->update($id,
                collect($data)->except(['photo'])->toArray() + [
                    'photo' => $this->verifyAndStoreImage($data['photo']),
                ]
            );
        }

        return $this->banner_repository->update($id, $data);
    }
}
