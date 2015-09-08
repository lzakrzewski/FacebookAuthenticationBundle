<?php

namespace Lucaszz\FacebookAuthenticationBundle\Model;

use FOS\UserBundle\Model\UserManagerInterface;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\TestUser;

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

        if (null !== $user) {
            return $user;
        }

        $user = new TestUser();
        $user->setUsername($fields['name']);
        $user->setEnabled(true);
        $user->setEmail('email@example.com');
        $user->setPassword('xyz');
        $user->setFacebookId($fields['id']);

        $this->users->updateUser($user);

        return $user;
    }
}
