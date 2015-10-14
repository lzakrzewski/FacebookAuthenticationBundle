<?php

namespace Lucaszz\FacebookAuthenticationBundle\Model;

use FOS\UserBundle\Model\UserManagerInterface;
use Lucaszz\FacebookAuthenticationBundle\Annotation\FacebookIdPropertyName;
use Lucaszz\FacebookAuthenticationBundle\Event\FacebookUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Lucaszz\FacebookAuthenticationBundle\Events;

class FacebookUsers
{
    /** @var UserManagerInterface */
    private $users;
    /** @var FacebookIdPropertyName */
    private $propertyName;
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * @param UserManagerInterface     $users
     * @param FacebookIdPropertyName   $propertyName
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(UserManagerInterface $users, FacebookIdPropertyName $propertyName, EventDispatcherInterface $dispatcher)
    {
        $this->users = $users;
        $this->propertyName = $propertyName;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param array $userNode
     *
     * @throws FacebookUserException
     *
     * @return UserInterface
     */
    public function get(array $userNode)
    {
        $user = $this->findUser($userNode['id']);

        if (null === $user) {
            return $this->createUser($userNode);
        }

        return $this->updateUser($user, $userNode);
    }

    private function createUser(array $fields)
    {
        /** @var UserInterface $user */
        $user = $this->users->createUser();

        $user->setFacebookId($fields['id']);
        $user->setUsername($fields['name']);
        $user->setEmail($fields['email']);
        $user->setEnabled(true);
        $user->setPassword(uniqid());

        $this->dispatcher->dispatch(Events::USER_CREATED, new FacebookUserEvent($user, $fields));

        $this->users->updateUser($user);

        return $user;
    }

    private function updateUser(UserInterface $user, array $fields)
    {
        $this->dispatcher->dispatch(Events::USER_UPDATED, new FacebookUserEvent($user, $fields));

        $this->users->updateUser($user);

        return $user;
    }

    private function findUser($userId)
    {
        $emptyUser = $this->users->createUser();

        if (!$emptyUser instanceof FacebookUser) {
            throw new FacebookUserException(sprintf('User could be only instance of \Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser, instance of %s given.', get_class($emptyUser)));
        }

        $propertyName = $this->propertyName->get($emptyUser);

        return $this->users->findUserBy(array($propertyName => $userId));
    }
}
