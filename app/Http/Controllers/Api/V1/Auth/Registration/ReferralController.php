<?php

namespace App\Http\Controllers\Api\V1\Auth\Registration;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Base\Constants\Auth\Role;
use App\Transformers\User\UserTransformer;
use App\Base\Constants\Masters\WalletRemarks;
use App\Transformers\User\ReferralTransformer;
use App\Http\Controllers\Api\V1\BaseController;
use App\Jobs\Notifications\AndroidPushNotification;

/**
 * @group SignUp-And-Otp-Validation
 *
 * APIs for User-Management
 */
class ReferralController extends BaseController
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
    * Get Referral code
    * @responseFile responses/auth/get-referral.json
    */
    public function index()
    {
        $user = fractal(auth()->user(), new ReferralTransformer);

        return $this->respondOk($user);
    }
    /**
    * Update User Referral
    * @bodyParam refferal_code string required refferal_code of the another user
    * @response {"success":true,"message":"success"}
    */
    public function updateUserReferral(Request $request)
    {
        // Validate Referral code
        $reffered_user = $this->user->belongsTorole(Role::USER)->where('refferal_code', $request->refferal_code)->first();
        if (!$reffered_user) {
            $this->throwCustomException('Provided Referral code is not valid', 'refferal_code');
        }

        // Update referred user's id to the users table
        auth()->user()->update(['referred_by'=>$reffered_user->id]);

        $user_wallet = $reffered_user->userWallet;
        $referral_commision = get_settings('referral_commision_for_user')?:0;

        $user_wallet->amount_added += $referral_commision;
        $user_wallet->amount_balance += $referral_commision;
        $user_wallet->save();

        // Add the history
        $reffered_user->userWalletHistory()->create([
            'amount'=>$referral_commision,
            'transaction_id'=>str_random(6),
            'remarks'=>WalletRemarks::REFERRAL_COMMISION,
            'refferal_code'=>$reffered_user->refferal_code,
            'is_credit'=>true]);

        // Notify user
        $title = trans('push_notifications.referral_earnings_notify_title',[],$reffered_user->lang);
        $body = trans('push_notifications.referral_earnings_notify_body',[],$reffered_user->lang);

        $reffered_user->notify(new AndroidPushNotification($title, $body));

        return $this->respondSuccess();
    }

    /**
    * Update Driver Referral code
    * @bodyParam refferal_code string required refferal_code of the another user
    * @response {"success":true,"message":"success"}
    */
    public function updateDriverReferral(Request $request)
    {
        $reffered_user = $this->user->belongsTorole(Role::DRIVER)->where('refferal_code', $request->refferal_code)->first();

        if (!$reffered_user) {
            $this->throwCustomException('Provided Referral code is not valid', 'refferal_code');
        }

        // Update referred user's id to the users table
        auth()->user()->update(['referred_by'=>$reffered_user->id]);

        // Add referral commission to the referred user
        $reffered_user = $reffered_user->driver;

        $driver_wallet = $reffered_user->driverWallet;
        $referral_commision = get_settings('referral_commision_for_driver')?:0;

        $driver_wallet->amount_added += $referral_commision;
        $driver_wallet->amount_balance += $referral_commision;
        $driver_wallet->save();

        // Add the history
        $reffered_user->driverWalletHistory()->create([
            'amount'=>$referral_commision,
            'transaction_id'=>str_random(6),
            'remarks'=>WalletRemarks::REFERRAL_COMMISION,
            'refferal_code'=>$reffered_user->refferal_code,
            'is_credit'=>true]);

        // Notify user
        $title = trans('push_notifications.referral_earnings_notify_title',$reffered_user->lang);
        $body = trans('push_notifications.referral_earnings_notify_body',$reffered_user->lang);

        $reffered_user->user->notify(new AndroidPushNotification($title, $body));

        return $this->respondSuccess();
    }
    /* *
        Add driver referal code as mobile number
    
    * */
    public function addDriverSignupReferral(Request $request)
    {
        $reffered_user = $this->user->belongsTorole(Role::DRIVER)->where('mobile', $request->refferal_mobile_num)->first();

        if (!$reffered_user) {
            $this->throwCustomException('Provided Referral mobile number does not exists', 'refferal_code');
        }

        // Update referred user's id to the users table
        auth()->user()->update(['driver_referred_by'=>$reffered_user->id, 'referral_commission_for_driver' => get_settings('referral_commision_for_driver')?:0]);
        return $this->respondSuccess();
        // Notify user
        $title = trans('push_notifications.referral_earnings_notify_title',$reffered_user->lang);
        $body = trans('push_notifications.referral_earnings_notify_body',$reffered_user->lang);

        $reffered_user->user->notify(new AndroidPushNotification($title, $body));

        return $this->respondSuccess();
    }
    /* *
        Get All driver referal
    
    * */
    // public function getDriverReferral(Request $request)
    // {
    //     $my_reffered_user = $this->user->belongsTorole(Role::DRIVER)->where('driver_referred_by', auth()->user()->id)->get();

    //     if (count($my_reffered_user) < 1) {
    //       return $this->respondNoContent();
    //     }

    //     $driversData = [];
    //     $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

    //     foreach($my_reffered_user as $user){

    //       $converted_current_date = Carbon::parse($user->created_at)->setTimezone($timezone)->format('jS M Y');
    //       $driversData[] = [
    //             'name' => $user->name ??'',
    //             'mobile' => $user->mobile ??'',
    //             'timezone' => $timezone,
    //             'joined_on_formated' => $converted_current_date,
    //             'joined_on_without_formated' => $user->created_at ??'',
    //             'is_active_driver' => $user->avtive ??'',
    //       ];
    //     }
    //     return response()->json(['success'=>true,'message'=>'driver_referral_list','data'=> $driversData]);

    //     // return $this->respondSuccess();
    // }

public function getDriverReferral(Request $request)
    {
        $my_reffered_user = $this->user->belongsTorole(Role::DRIVER)
        ->where('driver_referred_by', auth()->user()->id)
        ->whereHas('driver', function($query) {
            $query->where('approve', 1);
        })->paginate();

        if (count($my_reffered_user) < 1) {
           return $this->respondNoContent();
        }

        $driversData = [];
        $timezone = auth()->user()->timezone?:env('SYSTEM_DEFAULT_TIMEZONE');

        foreach($my_reffered_user as $user){

           $converted_current_date = Carbon::parse($user->created_at)->setTimezone($timezone)->format('jS M Y');
           $driversData[] = [
                'name' => $user->name ??'',
                'mobile' => $user->mobile ??'',
                'timezone' => $timezone,
                'joined_on_formated' => $converted_current_date,
                'joined_on_without_formated' => $user->created_at ??'',
                'is_active_driver' => $user->avtive ??'',
           ];           
        }
        $driversData['current_page'] = $my_reffered_user->currentPage();
        $driversData['total_page'] = $my_reffered_user->lastPage();

        return response()->json(['success'=>true,'message'=>'driver_referral_list','data'=> $driversData]);

        // return $this->respondSuccess();
    }

}
