<?php

namespace Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\Authentication;

use Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\Fake\FakeFacebookApi;
use Lzakrzewski\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;

class AuthenticationTest extends IntegrationTestCase
{
    /** @test */
    public function it_redirects_to_facebook_dialog_page()
    {
        $this->visit('/facebook/login');

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\RedirectResponse', $this->client->getResponse());

        $query = $this->redirectResponseQuery();

        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('client_id', $query);
    }

    /** @test */
    public function it_authorize_new_facebook_user()
    {
        $this->visit('/facebook/login?code=1234');

        $this->assertIsAuthorizedAsUser('FacebookUser');
        $this->assertThatLogWithMessageWasCreated('has been authenticated successfully');
    }

    /** @test */
    public function it_authorize_existing_facebook_user()
    {
        $this->user('FacebookUser', 'test1', 'facebook-user@example.com', 123456);

        $this->visit('/facebook/login?code=1234');

        $this->assertIsAuthorizedAsUser('FacebookUser');
        $this->assertThatLogWithMessageWasCreated('has been authenticated successfully');
    }

    /** @test */
    public function it_does_not_authorize_facebook_user_when_problem_with_api_occurs()
    {
        $this->user('FacebookUser', 'test1', 'facebook-user@example.com', 123456);

        FakeFacebookApi::problemWithApiOccurs();

        $this->visit('/facebook/login?code=1234');

        $this->assertIsNotAuthorizedAsUser();
        $this->assertThatLogWithMessageWasCreated('Authentication request failed');
    }

    /** @test */
    public function it_can_be_authorized_with_form_login_and_valid_credentials()
    {
        $this->user('john-doe', 'test1', 'john-doe@example.com');

        $this->visitRoute('fos_user_security_login');
        $this->fillAndSubmitLoginForm('john-doe', 'test1');

        $this->assertIsAuthorizedAsUser('john-doe');
    }

    /** @test */
    public function it_can_not_be_authorized_with_form_login_and_invalid_credentials()
    {
        $this->user('john-doe', 'test1', 'john-doe@example.com');

        $this->visitRoute('fos_user_security_login');
        $this->fillAndSubmitLoginForm('wrong-username', 'test1');

        $this->assertIsNotAuthorizedAsUser();
    }

    /** {@inheritdoc} */
    protected function setUp()
    {
        parent::setUp();

        $this->setupDatabase();
    }

    private function fillAndSubmitLoginForm($username, $password)
    {
        $form = $this->crawler->selectButton('_submit')->form();

        $this->client->submit($form, array('_username' => $username, '_password' => $password));
    }

    private function assertIsAuthorizedAsUser($username)
    {
        $this->assertEquals($username, $this->currentUserName());
    }

    private function assertIsNotAuthorizedAsUser()
    {
        $this->assertEquals('anon.', $this->currentUserName());
    }

    private function redirectResponseQuery()
    {
        $parsedUrl = parse_url($this->client->getResponse()->getTargetUrl());
        parse_str($parsedUrl['query'], $parsedQuery);

        return $parsedQuery;
    }

    private function currentUserName()
    {
        $this->visitRoute('fos_user_security_login');

        return (string) $this->client->getContainer()->get('security.context')->getToken()->getUser();
    }
}
