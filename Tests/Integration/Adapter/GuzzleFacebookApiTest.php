<?php

namespace Lucaszz\FacebookAuthenticationBundle\Tests\Integration\Adapter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use Lucaszz\FacebookAuthenticationBundle\Adapter\GuzzleFacebookApi;
use Lucaszz\FacebookAuthenticationBundle\Tests\Integration\IntegrationTestCase;

class GuzzleFacebookApiTest extends IntegrationTestCase
{
    /** @var GuzzleFacebookApi */
    private $adapter;
    /** @var ClientInterface */
    private $guzzleClient;
    /** @var History */
    private $history;

    /**
     * @test
     */
    public function it_requests_for_access_token_successfully()
    {
        $this->thereIsSuccessfullFacebookApiResponse();

        $accessToken = $this->adapter->accessToken('correct-code');

        $this->assertEquals('access-token', $accessToken);
        $this->assertThatRequestIsEqual($this->expectedSuccessfulAccessTokenRequest(), $this->history->getLastRequest());
    }

    /**
     * @test
     *
     * @expectedException \Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException
     */
    public function it_fails_when_unable_to_parse_token_from_response_during_requesting_for_access_token()
    {
        $this->thereIsFacebookApiResponseWithWrongToken();

        try {
            $this->adapter->accessToken('correct-code');
        } catch (\Exception $e) {
            $this->assertThatLogWithMessageWasCreated('Unable to get access token from response');

            throw $e;
        }
    }

    /**
     * @test
     *
     * @expectedException \Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException
     */
    public function it_fails_when_facebook_api_throws_an_exception_during_requesting_for_access_token()
    {
        $this->thereIsFacebookApiException();

        try {
            $this->adapter->accessToken('correct-code');
        } catch (\Exception $e) {
            $this->assertThatLogWithMessageWasCreated('An error with facebook graph api occurred');

            throw $e;
        }
    }

    /**
     * @test
     *
     * @expectedException \Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException
     */
    public function it_fails_when_facebook_api_returns_unsuccessful_response_during_requesting_for_access_token()
    {
        $this->thereIsFacebookApiUnsuccessfulResponse();

        try {
            $this->adapter->accessToken('correct-code');
        } catch (\Exception $e) {
            $this->assertThatLogWithMessageWasCreated('An error with facebook graph api occurred');

            throw $e;
        }
    }

    /**
     * @test
     */
    public function it_can_retrieve_me_fields_successfully()
    {
        $fields = $this->adapter->me($this->accessToken());

        $this->assertThatRequiredMeFieldsExists($fields);
    }

    /**
     * @test
     *
     * @expectedException \Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException
     */
    public function it_fails_when_unable_to_parse_json_response_during_retrieving_me_fields()
    {
        $this->thereIsFacebookApiResponseWithWrongJson();

        try {
            $this->adapter->me($this->accessToken());
        } catch (\Exception $e) {
            $this->assertThatLogWithMessageWasCreated('Facebook graph api response body is not in JSON format');

            throw $e;
        }
    }

    /**
     * @test
     *
     * @expectedException \Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException
     */
    public function it_fails_when_facebook_api_throws_an_exception_during_retrieving_me_fields()
    {
        $this->thereIsFacebookApiException();

        try {
            $this->adapter->me($this->accessToken());
        } catch (\Exception $e) {
            $this->assertThatLogWithMessageWasCreated('An error with facebook graph api occurred');

            throw $e;
        }
    }

    /**
     * @test
     *
     * @expectedException \Lucaszz\FacebookAuthenticationBundle\Adapter\FacebookApiException
     */
    public function it_fails_when_facebook_api_returns_me_without_required_fields()
    {
        $this->thereIsFacebookApiWithoutRequiredMeFields();

        try {
            $this->adapter->me($this->accessToken());
        } catch (\Exception $e) {
            $this->assertThatLogWithMessageWasCreated('Facebook graph api should return response with all required fields');

            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->adapter = $this->container->get('lucaszz_facebook_authentication.adapter.facebook_api.guzzle');
        $this->guzzleClient = $this->container->get('lucaszz_facebook_authentication.guzzle');
        $this->history = new History();

        $this->guzzleClient->getEmitter()->attach($this->history);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        $this->adapter = null;
        $this->guzzleClient = null;
        $this->history = null;

        parent::tearDown();
    }

    private function thereIsSuccessfullFacebookApiResponse()
    {
        $body = array(
            'access_token' => 'access-token',
            'expires' => (string) (time() + 100),
        );

        $this->mockResponse(200, http_build_query($body));
    }

    private function thereIsFacebookApiResponseWithWrongToken()
    {
        $body = array(
            'xyz' => 'abcd',
            'expires' => (string) (time() + 100),
        );

        $this->mockResponse(200, http_build_query($body));
    }

    private function thereIsFacebookApiUnsuccessfulResponse()
    {
    }

    private function thereIsFacebookApiResponseWithWrongJson()
    {
        $this->mockResponse(200, 'xyz');
    }

    private function thereIsFacebookApiWithoutRequiredMeFields()
    {
        $this->mockResponse(200, json_encode(array('id' => '12345', 'name' => 'xyz')));
    }

    private function thereIsFacebookApiException()
    {
        $this->mockResponse(500);
    }

    private function accessToken()
    {
        $file = 'https://gist.githubusercontent.com/Lucaszz/a36984dd6691ab53092d/raw/e1b3c9168f1ea0e4e29c1e659627a4732ba5ce85/accessToken_1440969823';

        if ($resource = fopen($file, 'r')) {
            $accessToken = fgets($resource);
            fclose($resource);

            return $accessToken;
        }

        throw new \Exception('Unable to read file with access token');
    }

    private function expectedSuccessfulAccessTokenRequest()
    {
        $request = new Request('GET', 'https://graph.facebook.com/oauth/access_token');
        $query = $request->getQuery();

        $query->set('client_id', '1234');
        $query->set('redirect_uri', 'http://localhost/facebook/login');
        $query->set('client_secret', 'secret');
        $query->set('code', 'correct-code');

        return $request;
    }

    private function assertThatRequestIsEqual(RequestInterface $expectedRequest, RequestInterface $request)
    {
        $this->assertEquals($expectedRequest->getMethod(), $request->getMethod());
        $this->assertEquals($expectedRequest->getUrl(), $request->getUrl());
    }

    private function assertThatRequiredMeFieldsExists(array $fields)
    {
        $this->assertArrayHasKey('id', $fields);
        $this->assertNotNull($fields['id']);

        $this->assertArrayHasKey('email', $fields);
        $this->assertNotNull($fields['email']);

        $this->assertArrayHasKey('name', $fields);
        $this->assertNotNull($fields['name']);
    }

    private function mockResponse($status, $body = null)
    {
        $mock = new Mock();
        if ($status === 200) {
            $mock->addResponse(new Response($status, [], ($body === null) ? null : Stream::factory($body)));
        } else {
            $mock->addException(new RequestException('Exception', new Request('GET', 'http://graph.facebook.com/xyz')));
        }

        $this->container->get('lucaszz_facebook_authentication.guzzle')->getEmitter()->attach($mock);
    }
}
