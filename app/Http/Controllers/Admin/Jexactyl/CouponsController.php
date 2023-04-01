<?php

namespace Jexactyl\Http\Controllers\Admin\Jexactyl;

use Carbon\Carbon;
use Illuminate\View\View;
use Jexactyl\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Jexactyl\Exceptions\DisplayException;
use Jexactyl\Http\Controllers\Controller;
use Jexactyl\Exceptions\Model\DataValidationException;
use Jexactyl\Exceptions\Repository\RecordNotFoundException;
use Jexactyl\Contracts\Repository\SettingsRepositoryInterface;
use Jexactyl\Http\Requests\Admin\Jexactyl\Coupons\IndexFormRequest;
use Jexactyl\Http\Requests\Admin\Jexactyl\Coupons\StoreFormRequest;

class CouponsController extends Controller
{
    public function __construct(private readonly AlertsMessageBag $alert, private readonly SettingsRepositoryInterface $settings)
    {
    }

    public function index(): View
    {
        return view('admin.jexactyl.coupons', [
            'enabled' => $this->settings->get('jexactyl::coupons:enabled'),
            'coupons' => Coupon::query()->get(),
        ]);
    }

    /**
     * @throws DataValidationException
     * @throws RecordNotFoundException
     */
    public function update(IndexFormRequest $request): RedirectResponse
    {
        foreach ($request->normalize() as $key => $value) {
            $this->settings->set('jexactyl::coupons:' . $key, $value);
        }

        $this->alert->success('The coupons system has been successfully updated.')->flash();

        return redirect()->route('admin.jexactyl.coupons');
    }

    /**
     * @throws DisplayException
     */
    public function store(StoreFormRequest $request): RedirectResponse
    {
        $expires = $request->input('expires');

        if ($expires) {
            $time = Carbon::now();
            $expires_at = $time->addHours($request->input('expires'));
        } else {
            $expires_at = null;
        }

        if (Coupon::query()->where(['code' => $request->input('code')])->exists()) {
            throw new DisplayException('You cannot create a coupon with an already existing code.');
        }

        Coupon::query()->insert([
            'expires' => $expires_at,
            'created_at' => Carbon::now(),
            'code' => $request->input('code'),
            'uses' => $request->input('uses'),
            'cr_amount' => $request->input('credits'),
        ]);

        $this->alert->success('Successfully created a coupon.')->flash();

        return redirect()->route('admin.jexactyl.coupons');
    }
}
