<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Authentication;

use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Adapter\FakeFacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;

/**
 * @todo test case for api exception
 */
class AuthenticationTest extends IntegrationTestCase
{
    /**
     * @test
     */
    public function it_redirects_to_facebook_dialog_page()
    {
        $this->visit('/facebook/login');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $this->client->getResponse());

        $query = $this->redirectResponseQuery();

        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('client_id', $query);
    }

    /**
     * @test
     */
    public function it_authorize_new_facebook_user()
    {
        $this->visit('/facebook/login?code=1234');

        $this->assertIsAuthorizedAsUser('FacebookUser');
        $this->assertThatLogWithMessageWasCreated('has been authenticated successfully');
    }

    /**
     * @test
     */
    public function it_authorize_existing_facebook_user()
    {
        $this->user('FacebookUser', 'test1', 123456);

        $this->visit('/facebook/login?code=1234');

        $this->assertIsAuthorizedAsUser('FacebookUser');
        $this->assertThatLogWithMessageWasCreated('has been authenticated successfully');
    }

    /**
     * @test
     */
    public function it_does_not_authorize_facebook_user_when_problem_with_api_occurs()
    {
        $this->user('FacebookUser', 'test1', 123456);

        FakeFacebookApi::problemWithApiOccurs();

        $this->visit('/facebook/login?code=1234');

        $this->assertIsNotAuthorizedAsUser();
        $this->assertThatLogWithMessageWasCreated('Authentication request failed');
    }

    /**
     * @test
     */
    public function it_can_be_authorized_with_form_login_and_valid_credentials()
    {
        $this->user('john-doe', 'test1');

        $this->visitRoute('fos_user_security_login');
        $this->fillAndSubmitLoginForm('john-doe', 'test1');

        $this->assertIsAuthorizedAsUser('john-doe');
    }

    /**
     * @test
     */
    public function it_can_not_be_authorized_with_form_login_and_invalid_credentials()
    {
        $this->user('john-doe', 'test1');

        $this->visitRoute('fos_user_security_login');
        $this->fillAndSubmitLoginForm('wrong-username', 'test1');

        $this->assertIsNotAuthorizedAsUser();
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

    private function redirectResponseQuery()
    {
        $parsedUrl = parse_url($this->client->getResponse()->getTargetUrl());
        parse_str($parsedUrl['query'], $parsedQuery);

        return $parsedQuery;
    }

    private function assertThatLogWithMessageWasCreated($expectedMessage)
    {
        $logWasCreated = false;
        foreach ($this->logger->getLogs() as $log) {
            if (false !== strpos($log['message'], $expectedMessage)) {
                $logWasCreated = true;
                break;
            }
        }

        $this->assertTrue($logWasCreated);
    }
}
