<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Lzakrzewski\FacebookAuthenticationBundle\Annotation\FacebookIdPropertyName;
use Lzakrzewski\FacebookAuthenticationBundle\Tests\fixtures\TestUser;
use Lzakrzewski\FacebookAuthenticationBundle\Tests\fixtures\TestUserWithFacebookIdAnnotation;
use Lzakrzewski\FacebookAuthenticationBundle\Tests\fixtures\WrongUser;

class FacebookIdPropertyNameTest extends \PHPUnit_Framework_TestCase
{
    /** @var FacebookIdPropertyName */
    private $facebookIdFieldName;

    /**
     * @test
     */
    public function it_gets_annotated_property_name()
    {
        $fieldName = $this->facebookIdFieldName->get(new TestUserWithFacebookIdAnnotation());

        $this->assertEquals('id', $fieldName);
    }

    /**
     * @test
     */
    public function it_gets_default_property_name()
    {
        $fieldName = $this->facebookIdFieldName->get(new TestUser());

        $this->assertEquals('facebookId', $fieldName);
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function it_fails_when_no_property_and_annotation()
    {
        $this->facebookIdFieldName->get(new WrongUser());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->facebookIdFieldName = new FacebookIdPropertyName(new AnnotationReader());
    }
}
