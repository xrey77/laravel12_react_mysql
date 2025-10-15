<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\GenerateTwoFactorQrCode;
use Laravel\Fortify\Contracts\TwoFactorQrCodeGenerator;
use PragmaRX\Google2FALaravel\Google2FA as Google2FALaravel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('qrcode-uri', function ($app, $parameters) {
            // Manually build the otpauth URI from credentials
            $g2fa = new Google2FALaravel();
            $uri = $g2fa->get='QRCodeUrl'(
                config('app.name'),
                $parameters['email'],
                $parameters['secret']
            );

            // Generate the QR code SVG
            return QrCode::size(200)->generate($uri);
        });        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);
        Fortify::enableTwoFactorAuthentication(EnableTwoFactorAuthentication::class);
        
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
        
        $this->app->singleton(TwoFactorQrCodeGenerator::class, GenerateTwoFactorQrCode::class);
    }
}
