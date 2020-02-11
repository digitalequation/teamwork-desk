<?php

namespace DigitalEquation\TeamworkDesk\Services;

use DigitalEquation\TeamworkDesk\Exceptions\TeamworkHttpException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkParameterException;
use DigitalEquation\TeamworkDesk\HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class Tickets
{
    use HttpClient;

    /**
     * Get tickets priorities.
     *
     * @return array
     * @throws \DigitalEquation\TeamworkDesk\Exceptions\TeamworkHttpException
     */
    public function priorities(): array
    {
        try {
            /** @var Response $response */
            $response = $this->client->get('ticketpriorities.json');
            /** @var Stream $body */
            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get a list of tickets for a customer.
     *
     * @param int $customerId
     *
     * @return array
     * @throws \DigitalEquation\TeamworkDesk\Exceptions\TeamworkHttpException
     */
    public function customer($customerId): array
    {
        try {
            /** @var Response $response */
            $response = $this->client->get(sprintf('customers/%s/previoustickets.json', $customerId));
            /** @var Stream $body */
            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Send a ticket to teamwork desk.
     *
     * @param array $data
     *
     * @return array
     * @throws \DigitalEquation\TeamworkDesk\Exceptions\TeamworkHttpException
     */
    public function post($data): array
    {
        try {
            /** @var Response $response */
            $response = $this->client->post('tickets.json', [
                'form_params' => $data,
            ]);

            /** @var Stream $body */
            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Post a reply to a ticket.
     *
     * @param array $data
     *
     * @return array
     * @throws \DigitalEquation\TeamworkDesk\Exceptions\TeamworkHttpException
     * @throws \DigitalEquation\TeamworkDesk\Exceptions\TeamworkParameterException
     */
    public function reply(array $data): array
    {
        if (empty($data['ticketId'])) {
            throw new TeamworkParameterException('The `reply` method expects the passed array param to contain `ticketId`', 400);
        }

        try {
            /** @var Response $response */
            $response = $this->client->post(sprintf('tickets/%s.json', $data['ticketId']), [
                'form_params' => $data,
            ]);

            /** @var Stream $body */
            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage());
        }
    }

    /**
     * Get ticket by id.
     *
     * @param int $ticketId
     *
     * @return array
     * @throws \DigitalEquation\TeamworkDesk\Exceptions\TeamworkHttpException
     */
    public function ticket($ticketId): array
    {
        try {
            /** @var Response $response */
            $response = $this->client->get(sprintf('tickets/%s.json', $ticketId));
            /** @var Stream $body */
            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }
}
