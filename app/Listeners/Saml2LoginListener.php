<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Saml2LoginListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $messageId = $event->getSaml2Auth()->getLastMessageId();
        // your own code preventing reuse of a $messageId to stop replay attacks
        $user = $event->getSaml2User();
        dd($user);
        $userData = [
            'id' => $user->getUserId(),
            'attributes' => $user->getAttributes(),
            'assertion' => $user->getRawSamlAssertion()
        ];

        $attributes = $user->getAttributes();
        $email = $attributes['EmailAddress'][0];
        $first_name = $attributes['FirstName'][0];
        $last_name = $attributes['LastName'][0];
        $user = User::firstOrCreate(
            ['user_id' => $email ],
            [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email
            ]
        );

        Auth::login($user);

    }
}
