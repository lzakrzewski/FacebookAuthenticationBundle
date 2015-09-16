<?php

namespace Lucaszz\FacebookAuthenticationBundle\Model;

use FOS\UserBundle\Model\UserManagerInterface;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\TestUser;

/**
 * @todo add events
 */
class FacebookUsers
{
    /** @var UserManagerInterface */
    private $users;

    /**
     * @param UserManagerInterface $users
     */
    public function __construct(UserManagerInterface $users)
    {
        $this->users = $users;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $fields)
    {
        $user = $this->users->findUserBy(array('facebookId' => $fields['id']));

        if (null === $user) {
            $user = new TestUser();
            $user->setFacebookId($fields['id']);
        }

        $user->setUsername($fields['name']);
        $user->setEmail($fields['email']);

        $user->setEnabled(true);
        $user->setPassword(uniqid());

        $this->users->updateUser($user);

        return $user;
    }
}
