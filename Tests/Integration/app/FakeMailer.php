<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\app;

use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;

class FakeMailer implements MailerInterface
{
    /** {@inheritdoc} */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
    }

    /** {@inheritdoc} */
    public function sendResettingEmailMessage(UserInterface $user)
    {
    }
}
