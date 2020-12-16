<?php

namespace DigitalEquation\TeamworkDesk\Tests;

use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskParameterException;
use DigitalEquation\TeamworkDesk\Services\TicketService;
use GuzzleHttp\Exception\ClientException;

class TeamworkTicketsTest extends TeamworkTestCase
{
    /** @test */
    public function it_should_throw_a_client_exception_on_priorities_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->priorities();
    }

    /** @test
     * @throws \JsonException
     */
    public function it_should_return_all_priorities(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Tickets/priorities-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

        self::assertEquals($body, json_encode($response->priorities(), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_customer_tickets_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->customer(52);
    }

    /** @test
     * @throws \JsonException
     */
    public function it_should_return_a_list_of_customer_tickets(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Tickets/customer-tickets-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

        self::assertEquals($body, json_encode($response->customer(529245), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_ticket_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->ticket(6545);
    }

    /** @test
     * @throws \JsonException
     */
    public function it_should_return_a_ticket(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Tickets/ticket-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

        self::assertEquals($body, json_encode($response->ticket(6546545), JSON_THROW_ON_ERROR));
    }

    /** @test */
    public function it_should_throw_a_client_exception_on_create_ticket_request(): void
    {
        $this->app['config']->set('teamwork.desk.domain', 'undefined');

        $this->expectException(ClientException::class);
        (new TicketService)->post([]);
    }

    /** @test
     * @throws \JsonException
     */
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

        $body     = file_get_contents(__DIR__ . '/Mock/Tickets/create-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

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

    /** @test
     * @throws \JsonException
     */
    public function it_should_post_a_reply_and_return_back_the_ticket(): void
    {
        $body     = file_get_contents(__DIR__ . '/Mock/Tickets/ticket-reply-response.json');
        $client   = $this->mockClient(200, $body);
        $response = new TicketService($client);

        self::assertEquals($body, json_encode($response->reply([
            'ticketId'   => 2201568,
            'body'       => 'Reply TEST on ticket.',
            'customerId' => 65465,
        ]), JSON_THROW_ON_ERROR));
    }
}
