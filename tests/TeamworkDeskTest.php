<?php

namespace DigitalEquation\TeamworkDesk\Tests;

use DigitalEquation\TeamworkDesk\Exceptions\TeamworkHttpException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkInboxException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkUploadException;
use DigitalEquation\TeamworkDesk\Services\TicketsService;

class TeamworkDeskTest extends TeamworkTestCase
{
    /** @test */
    public function it_should_throw_an_http_exception_on_user_request()
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        (new TicketsService)->me();
    }

    /** @test */
    public function it_should_return_the_logged_in_user()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Me/response-body.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketsService($client);

        $this->assertEquals($body, json_encode($response->me()));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_inboxes_request()
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        (new TicketsService)->inboxes();
    }

    /** @test */
    public function it_should_return_an_array_of_inboxes()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Desk/inboxes-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketsService($client);

        $this->assertEquals($body, json_encode($response->inboxes()));
    }

    /** @test */
    public function it_should_throw_an_inbox_exception()
    {
        $this->expectException(TeamworkInboxException::class);

        $body     = file_get_contents(__DIR__ . '/Mock/Desk/inboxes-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketsService($client);
        $response->inbox('undefined-inbox-name');
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_inbox_request()
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);
        $this->tickets->inbox('undefined');
    }

    /** @test */
    public function it_should_return_the_inbox_data()
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Desk/inboxes-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketsService($client);

        $inboxResponse = file_get_contents(__DIR__ . '/Mock/Desk/inbox-response.json');
        $this->assertEquals($inboxResponse, json_encode($response->inbox('Inbox 1')));
    }

    /** @test */
    public function it_should_throw_an_upload_exception_on_post_upload_request()
    {
        $this->expectException(TeamworkUploadException::class);

        (new TicketsService)->upload(24234, '');
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_post_upload_request()
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(TeamworkHttpException::class);

        $request = $this->getUploadFileRequest('file');
        (new TicketsService)->upload(423423, $request->file);
    }

    /** @test */
    public function it_should_upload_a_file_and_return_the_attachment_id()
    {
        $request = $this->getUploadFileRequest('files', true);
        $file    = $request->file('files')[0];

        $body     = file_get_contents(__DIR__ . '/Mock/Desk/upload-data.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketsService($client);

        $uploadResponse = file_get_contents(__DIR__ . '/Mock/Desk/upload-response.json');
        $this->assertEquals($uploadResponse, json_encode($response->upload(6546545, $file)));
    }
}
