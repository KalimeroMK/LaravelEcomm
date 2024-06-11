<?php

namespace Modules\Newsletter\Service;

use Modules\Core\Service\CoreService;
use Modules\Newsletter\Repository\NewsletterRepository;

class NewsletterService extends CoreService
{
    public NewsletterRepository $newsletter_repository;

    public function __construct(NewsletterRepository $newsletter_repository)
    {
        parent::__construct($newsletter_repository);
        $this->newsletter_repository = $newsletter_repository;
    }
}
