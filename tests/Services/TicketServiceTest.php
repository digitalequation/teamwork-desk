<?php

namespace DigitalEquation\TeamworkDesk\Tests;

use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskInboxException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskParameterException;
use DigitalEquation\TeamworkDesk\Services\TicketService;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class TicketServiceTest extends TeamworkTestCase
{
    /** @test */
    public function it_should_throw_a_client_exception_on_user_request(): void
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->me();
    }

    /** @test */
    public function it_should_return_the_logged_in_user(): void
    {
        [$body, $response] = $this->mockService(__DIR__ . '/../Mock/Me/response-body.json');

        self::assertEquals($body, json_encode($response->me(), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_inboxes_request(): void
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->inboxes();
    }

    /** @test */
    public function it_should_return_an_array_of_inboxes(): void
    {
        [$body, $response] = $this->mockService(__DIR__ . '/../Mock/Desk/inboxes-response.json');

        self::assertEquals($body, json_encode($response->inboxes(), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_inbox_exception(): void
    {
        $this->expectException(TeamworkDeskInboxException::class);

        [, $response] = $this->mockService(__DIR__ . '/../Mock/Desk/inboxes-response.json');
        $response->inbox('undefined-inbox-name');
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_inbox_request(): void
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        $this->tickets->inbox('undefined');
    }

    /** @test */
    public function it_should_return_the_inbox_data(): void
    {
        [, $response] = $this->mockService(__DIR__ . '/../Mock/Desk/inboxes-response.json');
        $inboxResponse = file_get_contents(__DIR__ . '/../Mock/Desk/inbox-response.json');

        self::assertEquals($inboxResponse, json_encode($response->inbox('Inbox 1'), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_upload_exception_on_post_upload_request(): void
    {
        $this->expectException(FileNotFoundException::class);

        $file = new UploadedFile('./', '');

        (new TicketService)->upload(24234, $file);
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_post_upload_request(): void
    {
        $this->app['config']->set('teamwork-desk.domain', 'undefined');

        $this->expectException(ClientException::class);

        $request = $this->getUploadFileRequest('file');
        (new TicketService)->upload(423423, $request->file);
    }

    /** @test */
    public function it_should_upload_a_file_and_return_the_attachment_id(): void
    {
        $request = $this->getUploadFileRequest('files', true);
        $file    = $request->file('files')[0] ?? null;

        [, $response] = $this->mockService(__DIR__ . '/../Mock/Desk/upload-data.json');
        $uploadResponse = file_get_contents(__DIR__ . '/../Mock/Desk/upload-response.json');

        self::assertEquals($uploadResponse, json_encode($response->upload(6546545, $file), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_priorities_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->priorities();
    }

    /** @test */
    public function it_should_return_all_priorities(): void
    {
        [$body, $response] = $this->mockService(__DIR__ . '/../Mock/Tickets/priorities-response.json');

        self::assertEquals($body, json_encode($response->priorities(), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_customer_tickets_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->customer(52);
    }

    /** @test */
    public function it_should_return_a_list_of_customer_tickets(): void
    {
        [$body, $response] = $this->mockService(__DIR__ . '/../Mock/Tickets/customer-tickets-response.json');

        self::assertEquals($body, json_encode($response->customer(529245), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_ticket_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->ticket(6545);
    }

    /** @test */
    public function it_should_return_a_ticket(): void
    {
        [$body, $response] = $this->mockService(__DIR__ . '/../Mock/Tickets/ticket-response.json');

        self::assertEquals($body, json_encode($response->ticket(6546545), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_create_ticket_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->post([]);
    }

    /** @test */
    public function it_should_create_a_ticket(): void
    {
        $data = [
            'assignedTo'          => 5465,
            'inboxId'             => 5545,
            'tags'                => 'Test ticket',
            'priority'            => 'low',
            'status'              => 'active',
            'source'              => 'Email (Manual)',
            'customerFirstName'   => 'Test',
            'customerLastName'    => 'User',
            'customerEmail'       => 'test.user@email.com',
            'customerPhoneNumber' => '',
            'subject'             => 'TEST',
            'previewTest'         => 'This is an API test.',
            'message'             => 'Ths is an API test so please ignore this ticket.',
        ];

        [$body, $response] = $this->mockService(__DIR__ . '/../Mock/Tickets/create-response.json');

        self::assertEquals($body, json_encode($response->post($data), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_an_parameter_exception_on_ticket_reply_request(): void
    {
        $this->expectException(TeamworkDeskParameterException::class);
        (new TicketService)->reply([]);
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_ticket_reply_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->reply(['ticketId' => 1]);
    }

    /** @test */
    public function it_should_post_a_reply_and_return_back_the_ticket(): void
    {
        [$body, $response] = $this->mockService(__DIR__ . '/../Mock/Tickets/ticket-reply-response.json');

        self::assertEquals($body, json_encode($response->reply([
            'ticketId'   => 2201568,
            'body'       => 'Reply TEST on ticket.',
            'customerId' => 65465,
        ]), JSON_THROW_ON_ERROR));
    }

    private function mockService(string $mockData): array
    {
        $body   = file_get_contents($mockData);
        $client = $this->mockClient(200, $body);

        return [$body, new TicketService($client)];
    }
}
