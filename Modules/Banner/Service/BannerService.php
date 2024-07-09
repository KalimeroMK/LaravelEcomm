<?php

namespace Modules\Banner\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Service\CoreService;

class BannerService extends CoreService
{
    public BannerRepository $banner_repository;

    public function __construct(BannerRepository $banner_repository)
    {
        parent::__construct($banner_repository);
        $this->banner_repository = $banner_repository;
    }

    /**
     * Create a new banner with possible media files.
     *
     * @param  array<string, mixed>  $data  The data for creating the banner.
     * @param  Request|null          $request  The request containing the files.
     * @return Model The newly created banner model.
     */
    public function create(array $data, Request $request = null): Model
    {
        return $this->banner_repository->create($data);
    }

    /**
     * Update an existing banner with new data and possibly new media files.
     *
     * @param  int                   $id  The banner ID to update.
     * @param  array<string, mixed>  $data  The data for updating the banner.
     * @param  Request|null          $request  The request containing the files.
     * @return Model The updated banner model.
     */
    public function update(int $id, array $data, Request $request = null): Model
    {
        $banner = $this->banner_repository->findById($id);

        $banner->update($data);
        
        return $banner;
    }
}
