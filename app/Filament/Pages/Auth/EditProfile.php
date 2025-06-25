<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('profile_picture')
                    ->image()
                    ->avatar()
                    ->visibility('public')
                    ->directory('avatars')
                    ->disk('public')
                    ->maxSize(1024)
                    ->nullable(),

                TextInput::make('phone_number')
                    ->nullable()
                    ->maxLength(255),

                TextInput::make('email')
                    ->nullable()
                    ->email()
                    ->maxLength(255),

                $this->getNameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
