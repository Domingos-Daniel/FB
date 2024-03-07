<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;

class EditProfile extends BaseEditProfile
{
    public function getView(): string
    {
        return static::$view ?? 'filament.pages.auth.edit-profile';
    }

    public function getLayout(): string
    {
        return static::$layout ?? (static::isSimple() ? 'filament-panels::components.layout.index' : 'filament-panels::components.layout.index');
    }

    protected function hasFullWidthFormActions(): bool
    {
        return true;
    }

    public static function getSlug(): string
    {
        return static::$slug ?? 'me';
    }
    
    public function form(Form $form): Form
    {
        
        return $form
            ->schema([
                // TextInput::make('username')
                //     ->required()
                //     ->maxLength(255),
                Section::make('Personal Information')
                    
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                
            ]);
    }
}
