<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\fixtures;

use FOS\UserBundle\Model\User;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser;
use Lucaszz\FacebookAuthenticationBundle\Annotation as Lucaszz;

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
     * @Lucaszz\FacebookId
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
