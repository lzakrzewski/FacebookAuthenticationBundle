<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Event;

use FOS\UserBundle\Model\UserInterface;
use Lzakrzewski\FacebookAuthenticationBundle\Model\FacebookUser;
use Symfony\Component\EventDispatcher\Event;

final class FacebookUserEvent extends Event
{
    /** @var array */
    private $data;
    /** @var UserInterface */
    private $user;

    /**
     * @param UserInterface $user
     * @param array         $data
     */
    public function __construct(UserInterface $user, array $data)
    {
        $this->user = $user;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return UserInterface|FacebookUser
     */
    public function getUser()
    {
        return $this->user;
    }
}
