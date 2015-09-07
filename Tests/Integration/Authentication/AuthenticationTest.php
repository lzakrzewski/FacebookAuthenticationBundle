<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Authentication;

use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\TestUser;

class AuthenticationTest extends IntegrationTestCase
{
    /**
     * @test
     */
    public function it_redirects_to_facebook_dialog_page()
    {
        $this->visit($this->config['login_path']);

        $response = $this->client->getResponse();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $response);

        $parsedUrl = parse_url($response->getTargetUrl());
        parse_str($parsedUrl['query'], $parsedQuery);

        $this->assertArrayHasKey('redirect_uri', $parsedQuery);
        $this->assertArrayHasKey('scope', $parsedQuery);
        $this->assertArrayHasKey('client_id', $parsedQuery);
    }

    /**
     * @test
     */
    public function it_can_be_authorized_with_facebook_credentials()
    {
        $this->user('john-doe', 'test1');

        $this->visitRoute('fos_user_security_login');
        $this->fillAndSubmitLoginForm('john-doe', 'test1');

        $this->assertIsAuthorizedAsUser('john-doe');
    }

    /**
     * @test
     */
    public function it_can_not_be_authorized_with_facebook_credentials()
    {
        $this->user('john-doe', 'test1');

        $this->visitRoute('fos_user_security_login');
        $this->fillAndSubmitLoginForm('wrong-username', 'test1');

        $this->assertIsNotAuthorizedAsUser();
    }

    private function user($username, $password)
    {
        $user = new TestUser();

        $user->setPlainPassword($password);
        $user->setUsername($username);
        $user->setEmail('john@example.com');
        $user->setEnabled(true);

        $this->entityManager()->persist($user);
        $this->entityManager()->flush();

        return $user;
    }

    private function fillAndSubmitLoginForm($username, $password)
    {
        $form = $this->crawler->selectButton('_submit')->form();

        $this->client->submit($form, array('_username' => $username, '_password' => $password));
    }

    private function assertIsAuthorizedAsUser($username)
    {
        $securityData = $this->securityData();

        $this->assertEquals($username, $securityData['user']);
    }

    private function assertIsNotAuthorizedAsUser()
    {
        $securityData = $this->securityData();

        $this->assertEquals('Symfony\Component\Security\Core\Authentication\Token\AnonymousToken', $securityData['token_class']);
    }

    private function securityData()
    {
        $this->client->enableProfiler();
        $this->visitRoute('fos_user_security_login');

        return unserialize($this->client->getProfile()->getCollector('security')->serialize());
    }
}
