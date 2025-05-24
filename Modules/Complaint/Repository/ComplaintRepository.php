<?php

declare(strict_types=1);

namespace Modules\Complaint\Repository;

use Modules\Complaint\Models\Complaint;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;

class ComplaintRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Complaint::class);
    }
}
