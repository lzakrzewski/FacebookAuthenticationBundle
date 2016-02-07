<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Tests\fixtures;

use FOS\UserBundle\Model\User;
use Lzakrzewski\FacebookAuthenticationBundle\Model\FacebookUser;
use Lzakrzewski\FacebookAuthenticationBundle\Annotation as Lzakrzewski;

class TestUserWithFacebookIdAnnotation extends User implements FacebookUser
{
    /**
     * @AnotherAnnotation
     *
     * @var string
     */
    protected $property1 = 'value1';

    /**
     * @AnotherAnnotation
     * @Lzakrzewski\FacebookId
     * @AnotherAnnotation
     *
     * @var int
     */
    protected $id;

    /** @var string */
    public $property2 = 'value2';

    /**
     * {@inheritdoc}
     */
    public function getFacebookId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setFacebookId($facebookId)
    {
        $this->id = $facebookId;
    }

    /**
     * @return string
     */
    public function getProperty1()
    {
        return $this->property1;
    }

    /**
     * @param string $property1
     */
    public function setProperty1($property1)
    {
        $this->property1 = $property1;
    }

    /**
     * @return string
     */
    public function getProperty2()
    {
        return $this->property2;
    }

    /**
     * @param string $property2
     */
    public function setProperty2($property2)
    {
        $this->property2 = $property2;
    }
}
