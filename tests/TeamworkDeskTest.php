<?php

namespace DigitalEquation\TeamworkDesk\Tests;

use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskHttpException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskInboxException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskUploadException;
use DigitalEquation\TeamworkDesk\Services\TicketService;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class TeamworkDeskTest extends TeamworkTestCase
{
    /** @test */
    public function it_should_throw_an_http_exception_on_user_request(): void
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(TeamworkDeskHttpException::class);
        (new TicketService)->me();
    }

    /** @test
     * @throws \JsonException
     */
    public function it_should_return_the_logged_in_user(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Me/response-body.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

        self::assertEquals($body, json_encode($response->me(), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_inboxes_request(): void
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(TeamworkDeskHttpException::class);
        (new TicketService)->inboxes();
    }

    /** @test
     * @throws \JsonException
     */
    public function it_should_return_an_array_of_inboxes(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Desk/inboxes-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

        self::assertEquals($body, json_encode($response->inboxes(), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_inbox_exception(): void
    {
        $this->expectException(TeamworkDeskInboxException::class);

        $body     = file_get_contents(__DIR__ . '/Mock/Desk/inboxes-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);
        $response->inbox('undefined-inbox-name');
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_inbox_request(): void
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(TeamworkDeskHttpException::class);
        $this->tickets->inbox('undefined');
    }

    /** @test */
    public function it_should_return_the_inbox_data(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Desk/inboxes-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

        $inboxResponse = file_get_contents(__DIR__ . '/Mock/Desk/inbox-response.json');
        self::assertEquals($inboxResponse, json_encode($response->inbox('Inbox 1')));
    }

    /** @test */
    public function it_should_throw_an_upload_exception_on_post_upload_request(): void
    {
        $this->expectException(FileNotFoundException::class);

        $file = new UploadedFile('./', '');

        (new TicketService)->upload(24234, $file);
    }

    /** @test */
    public function it_should_throw_an_http_exception_on_post_upload_request(): void
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(TeamworkDeskHttpException::class);

        $request = $this->getUploadFileRequest('file');
        (new TicketService)->upload(423423, $request->file);
    }

    /** @test
     * @throws \JsonException
     */
    public function it_should_upload_a_file_and_return_the_attachment_id(): void
    {
        $request = $this->getUploadFileRequest('files', true);
        $file    = $request->file('files')[0] ?? null;

        $body     = file_get_contents(__DIR__ . '/Mock/Desk/upload-data.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

        $uploadResponse = file_get_contents(__DIR__ . '/Mock/Desk/upload-response.json');
        self::assertEquals($uploadResponse, json_encode($response->upload(6546545, $file), JSON_THROW_ON_ERROR));
    }
}
