<?php

namespace App\Filament\Admin\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

class CustomLogin extends BaseLogin
{
    use WithRateLimiting;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label("Email")
                    ->email()
                    ->required()
                    ->autocomplete('username')
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                TextInput::make('password')
                    ->label("Password")
                    ->password()
                    ->required()
                    ->revealable()
                    ->autocomplete('current-password')
                    ->extraInputAttributes(['tabindex' => 2]),
                Checkbox::make('remember')
                    ->label("Remember Me"),
            ])
            ->statePath('data');
    }   

    /**
     * Authenticate the user.
     *
     * @return \Filament\Http\Responses\Auth\Contracts\LoginResponse
     */
    public function authenticate(): LoginResponse
    {
        try {
            $this->rateLimit(5); // Batasi 5 percobaan dalam 1 menit
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages.auth.login.messages.throttled', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->danger()
                ->send();

            return app(LoginResponse::class);
        }

        $data = $this->form->getState();

        if (!Filament::auth()->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ], $data['remember'])) {
            // Jika otentikasi gagal
            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages.auth.login.messages.failed'),
            ]);
        }

        // $user = Auth::user();

        // if (!$user->is_approved) {
        //     Auth::logout(); // Penting: Logout user
        //     request()->session()->invalidate();
        //     request()->session()->regenerateToken();

        //     throw ValidationException::withMessages([
        //         'data.email' => 'Akun Anda belum disetujui oleh administrator.', // Pesan error kustom
        //     ]);
        // }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}