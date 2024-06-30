<?php

namespace Modules\Page\Service;

use Modules\Core\Service\CoreService;
use Modules\Page\Repository\PageRepository;

class PageService extends CoreService
{

    public PageRepository $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        parent::__construct($pageRepository);
        $this->pageRepository = $pageRepository;
    }

}