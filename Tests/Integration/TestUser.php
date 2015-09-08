<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_user")
 */
class TestUser extends BaseUser implements FacebookUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $facebookId;

    /**
     * {@inheritdoc}
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * {@inheritdoc}
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }
}
