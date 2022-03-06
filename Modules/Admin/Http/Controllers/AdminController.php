<?php

    namespace Modules\Admin\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Password\Store;
    use Carbon\Carbon;
    use DB;
    use Hash;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\JsonResponse;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Modules\Message\Models\Message;
    use Modules\User\Models\User;

    class AdminController extends Controller
    {
        /**
         * @return Application|Factory|View
         */
        public function index()
        {
            $data    = User::select(DB::raw("COUNT(*) as count"), DB::raw("DAYNAME(created_at) as day_name"),
                DB::raw("DAY(created_at) as day"))
                           ->where('created_at', '>', Carbon::today()->subDay(6))
                           ->groupBy('day_name', 'day')
                           ->orderBy('day')
                           ->get();
            $array[] = ['Name', 'Number'];
            foreach ($data as $key => $value) {
                $array[++$key] = [$value->day_name, $value->count];
            }

            //  return $data;
            return view('admin::index')->with('users', json_encode($array));
        }

        /**
         * @return Application|Factory|View
         */
        public function profile()
        {
            $profile = Auth()->user();

            return view('admin::profile', compact('profile'));
        }

        /**
         * @param  Request  $request
         * @param  User  $user
         *
         * @return RedirectResponse
         */
        public function profileUpdate(Request $request, User $user): RedirectResponse
        {
            $status = $user->update($request->all());
            if ($status) {
                request()->session()->flash('success', 'Successfully updated your profile');
            } else {
                request()->session()->flash('error', 'Please try again!');
            }

            return redirect()->back();
        }

        /**
         * @return Application|Factory|View
         */
        public function changePassword()
        {
            return view('backend.layouts.changePassword');
        }

        /**
         * @param  Store  $request
         *
         * @return RedirectResponse
         */
        public function changPasswordStore(Store $request): RedirectResponse
        {
            User::find(auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

            return redirect()->route('admin')->with('success', 'Password successfully changed');
        }

        // Pie chart
        public function userPieChart(Request $request)
        {
            $data    = User::select(\DB::raw("COUNT(*) as count"), DB::raw("DAYNAME(created_at) as day_name"),
                \DB::raw("DAY(created_at) as day"))
                           ->where('created_at', '>', Carbon::today()->subDay(6))
                           ->groupBy('day_name', 'day')
                           ->orderBy('day')
                           ->get();
            $array[] = ['Name', 'Number'];
            foreach ($data as $key => $value) {
                $array[++$key] = [$value->day_name, $value->count];
            }

            return view('admin::index')->with('course', json_encode($array));
        }

        /**
         * @return JsonResponse
         */
        public function messageFive(): JsonResponse
        {
            $message = Message::whereNull('read_at')->limit(5)->get();

            return response()->json($message);
        }

    }
