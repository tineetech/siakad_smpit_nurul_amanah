<?php

namespace App\Filament\Navigation;

use Filament\Navigation\NavigationItem;
use Illuminate\Support\Facades\Auth;
use Authorizable;


abstract class BaseNavigation
{
    abstract public function items(): array;

    protected function makeItem(string $label, string $url, string $icon, string $group = null, string $permission = null): NavigationItem
    {
        return NavigationItem::make($label)
            ->icon($icon)
            ->url($url)
            ->group($group)
            ->visible($permission ? Auth::user()->can($permission) : true);
    }
}
