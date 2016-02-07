<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\Fake;

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
