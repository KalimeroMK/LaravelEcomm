<?php

    namespace Modules\Admin\Repository;

    use Carbon\Carbon;
    use Illuminate\Contracts\Auth\Authenticatable;
    use Illuminate\Support\Facades\DB;
    use Modules\User\Models\User;

    class AdminRepository
    {
        /**
         * @return array
         */
        public function index(): array
        {
            $data    = User::select(
                DB::raw("COUNT(*) as count"),
                DB::raw("DAYNAME(created_at) as day_name"),
                DB::raw("DAY(created_at) as day")
            )
                           ->where('created_at', '>', Carbon::today()->subDay(6))
                           ->groupBy('day_name', 'day')
                           ->orderBy('day')
                           ->get();
            $array[] = ['Name', 'Number'];
            foreach ($data as $key => $value) {
                $array[++$key] = [$value->day_name, $value->count];
            }

            return $array;
        }

        /**
         * @return Authenticatable|null
         */
        public function profile(): ?Authenticatable
        {
            return Auth()->user();
        }

        public function profileUpdate()
        {
        }
    }
