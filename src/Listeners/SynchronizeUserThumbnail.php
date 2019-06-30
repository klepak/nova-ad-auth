<?php

namespace Klepak\NovaAdAuth\Listeners;

use Adldap\Laravel\Events\AuthenticatedWithWindows;

class SynchronizeUserThumbnail
{
    /**
     * Handle the event.
     *
     * @param AuthenticatedWithWindows $event
     *
     * @return void
     */
    public function handle(AuthenticatedWithWindows $event)
    {
        info("Synchronizing user thumbnail for '{$event->user->getCommonName()}'.");

        $thumbsPath = storage_path('app/public/user_thumbs');

        if(!file_exists($thumbsPath))
            mkdir($thumbsPath);

        @file_put_contents($thumbsPath . '/' . $event->model->id . '.png', $event->user->getThumbnail());
    }
}
