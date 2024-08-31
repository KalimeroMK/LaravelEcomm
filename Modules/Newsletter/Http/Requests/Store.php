<?php

namespace Modules\Newsletter\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;
use Modules\Newsletter\Http\Roles\MailboxValidEmail;

class Store extends CoreRequest
{
    protected MailboxValidEmail $mailboxValidEmail;

    public function __construct(MailboxValidEmail $mailboxValidEmail)
    {
        parent::__construct();
        $this->mailboxValidEmail = $mailboxValidEmail;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'email', 'unique:newsletters'],
        ];

        if (app()->environment('production')) {
            $rules['email'][] = $this->mailboxValidEmail;
        }

        return $rules;
    }
}
