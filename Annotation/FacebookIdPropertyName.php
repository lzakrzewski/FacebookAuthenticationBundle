<?php

namespace Lucaszz\FacebookAuthenticationBundle\Annotation;

use FOS\UserBundle\Model\UserInterface;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser;
use Doctrine\Common\Annotations\Reader;

class FacebookIdPropertyName
{
    const FACEBOOK_ID_DEFAULT_PROPERTY_NAME = 'facebookId';

    /** @var Reader */
    private $reader;

    /**
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param UserInterface $user
     *
     * @return string
     */
    public function get(UserInterface $user)
    {
        $properties = $this->properties($user);

        foreach ($properties as $property) {
            $annotations = $this->reader->getPropertyAnnotations($property);

            if (in_array(new FacebookId(), $annotations)) {
                return $property->getName();
            }
        }

        if (!property_exists($user, self::FACEBOOK_ID_DEFAULT_PROPERTY_NAME)) {
            throw new \InvalidArgumentException(sprintf('Property "facebookId" does not exist. Instance of FacebookUser should have "facebookId" property or "FacebookId" annotation.'));
        }

        return self::FACEBOOK_ID_DEFAULT_PROPERTY_NAME;
    }

    private function properties(FacebookUser $user)
    {
        $reflection = new \ReflectionClass($user);

        return $reflection->getProperties();
    }
}
