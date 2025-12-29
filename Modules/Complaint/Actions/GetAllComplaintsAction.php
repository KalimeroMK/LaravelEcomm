<?php

declare(strict_types=1);

namespace Modules\Complaint\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Repository\ComplaintRepository;

readonly class GetAllComplaintsAction
{
    public function __construct(private ComplaintRepository $repository) {}

    public function execute(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $user = Auth::user();

        if (! $user) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        // Admin и super-admin можат да гледаат сите complaints
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            return Complaint::with(['user', 'order'])->orderBy('id', 'desc')->paginate(20);
        }

        // Обични корисници гледаат само свои complaints
        return Complaint::with(['user', 'order'])->where('user_id', $user->id)->orderBy('id', 'desc')->paginate(20);
    }
}
