<?php

    namespace Modules\Settings\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Modules\Admin\Http\Requests\Update;
    use Modules\Admin\Models\Setting;

    class SettingsController extends Controller
    {
        /**
         * @return Application|Factory|View
         */
        public function index()
        {
            $data = Setting::first();

            return view('admin::setting', compact('data'));
        }

        /**
         * @param  Update  $request
         *
         * @return RedirectResponse
         */
        public function Update(Update $request): RedirectResponse
        {
            $settings = Setting::first();
            $status   = $settings->update($request->all());
            if ($status) {
                request()->session()->flash('success', 'Setting successfully updated');
            } else {
                request()->session()->flash('error', 'Please try again');
            }

            return redirect()->route('admin');
        }
    }
