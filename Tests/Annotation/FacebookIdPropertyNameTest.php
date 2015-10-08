<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Lucaszz\FacebookAuthenticationBundle\Annotation\FacebookIdPropertyName;
use Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\TestUser;
use Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\TestUserWithFacebookIdAnnotation;
use Lucaszz\FacebookAuthenticationBundle\Tests\fixtures\WrongUser;

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
