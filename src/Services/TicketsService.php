<?php

namespace DigitalEquation\TeamworkDesk\Services;

use DigitalEquation\TeamworkDesk\Exceptions\TeamworkHttpException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkInboxException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkParameterException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkUploadException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\File;

class TicketsService
{
    public Client $client;

    public function __construct($client = null)
    {
        if ($client instanceof Client) {
            $this->client = $client;
        } else {
            $this->client = new Client([
                'base_uri' => sprintf('https://%s.teamwork.com/desk/v1/', config('teamwork-desk.domain')),
                'auth'     => [config('teamwork-desk.key'), ''],
            ]);
        }
    }

    /**
     * Get tickets priorities.
     *
     * @return array
     * @throws TeamworkHttpException
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
     * @throws TeamworkHttpException
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
     * @throws TeamworkHttpException
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
     * @throws TeamworkHttpException
     * @throws TeamworkParameterException
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
     * @throws TeamworkHttpException
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

    /**
     * Return an inbox by name.
     *
     * @param string $name
     *
     * @return array
     * @throws TeamworkHttpException
     * @throws TeamworkInboxException
     */
    public function inbox(string $name): array
    {
        try {
            /** @var Response $response */
            $response = $this->client->get('inboxes.json');
            /** @var Stream $body */
            $body    = $response->getBody();
            $inboxes = json_decode($body->getContents(), true);

            $inbox = collect($inboxes['inboxes'])->first(
                function ($inbox) use ($name) {
                    return $inbox['name'] === $name;
                }
            );

            if (!$inbox) {
                throw new TeamworkInboxException("No inbox found with the name: $name!", 400);
            }

            return $inbox;
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Get teamwork desk inboxes.
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function inboxes(): array
    {
        try {
            /** @var Response $response */
            $response = $this->client->get('inboxes.json');
            /** @var Stream $body */
            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Return the current client info.
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function me(): array
    {
        try {
            /** @var Response $response */
            $response = $this->client->get('me.json');
            /** @var Stream $body */
            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Update the customer, based on customerId.
     *
     * @param array $data = ['customerId', 'email', 'firstName', 'lastName', 'phone', 'mobile'];
     *
     * @return array
     * @throws TeamworkHttpException
     */
    public function postCustomer($data): array
    {
        try {
            /** @var Response $response */
            $response = $this->client->put('customers/' . $data['customerId'] . '.json', [
                'json' => $data,
            ]);

            /** @var Stream $body */
            $body = $response->getBody();

            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }

    /**
     * Upload file to teamwork desk.
     *
     * @param $userId
     * @param $file
     *
     * @return array
     * @throws TeamworkHttpException
     * @throws TeamworkUploadException
     */
    public function upload($userId, $file): array
    {
        if (empty($file)) {
            throw new TeamworkUploadException('No file provided.', 400);
        }

        $filename  = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $path      = sys_get_temp_dir();
        $temp      = $file->move($path, $filename);
        $stream    = fopen($temp->getPathName(), 'r');

        try {
            /** @var Response $response */
            $response = $this->client->post('upload/attachment', [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => $stream,
                    ], [
                        'name'     => 'userId',
                        'contents' => $userId,
                    ],
                ],
            ]);
            /** @var Stream $body */
            $body = $response->getBody();
            $body = json_decode($body->getContents(), true);

            if (!empty($stream)) {
                File::delete($temp->getPathName());
            }

            return [
                'id'        => $body['attachment']['id'],
                'url'       => $body['attachment']['downloadURL'],
                'extension' => $extension,
                'name'      => $body['attachment']['filename'],
                'size'      => $body['attachment']['size'],
            ];
        } catch (ClientException $e) {
            throw new TeamworkHttpException($e->getMessage(), 400);
        }
    }
}