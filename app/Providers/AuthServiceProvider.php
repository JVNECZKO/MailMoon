<?php

namespace App\Providers;

use App\Models\Campaign;
use App\Models\CampaignMessage;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\SendingIdentity;
use App\Models\Template;
use App\Policies\OwnedResourcePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        foreach ([SendingIdentity::class, ContactList::class, Contact::class, Template::class, Campaign::class, CampaignMessage::class] as $model) {
            Gate::policy($model, OwnedResourcePolicy::class);
        }
    }
}
