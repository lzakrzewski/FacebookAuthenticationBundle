<?php

namespace Lucaszz\FacebookAuthenticationBundle;

final class Events
{
    /**
     * Dispatched after new user is created with data retrieved from /me endpoint
     * Hook into this event to modify new user with facebook graph api data.
     */
    const USER_CREATED = 'lucaszz_facebook_authentication.on_user_created';

    /**
     * Dispatched after existing user loaded from repository and updated with data retrieved from /me endpoint
     * Hook into this event to modify existing user with facebook graph api data.
     */
    const USER_UPDATED = 'lucaszz_facebook_authentication.on_user_updated';
}
