<?php

namespace Lucaszz\FacebookAuthenticationBundle;

use Lucaszz\FacebookAuthenticationBundle\DependencyInjection\Factory\FacebookFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LucaszzFacebookAuthenticationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new FacebookFactory());
    }
}
