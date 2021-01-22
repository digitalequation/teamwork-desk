<?php

namespace DigitalEquation\TeamworkDesk\Services;

use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskHttpException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskInboxException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskJsonException;
use DigitalEquation\TeamworkDesk\Exceptions\TeamworkDeskParameterException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Psr\Http\Message\StreamInterface;

class TicketService
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

    public function priorities(): ?array
    {
        return $this->getResponse(
            $this->client->get('ticketpriorities.json')->getBody()
        );
    }

    public function customer(int $customerId): array
    {
        return $this->getResponse(
            $this->client->get(sprintf('customers/%s/previoustickets.json', $customerId))->getBody()
        );
    }

    public function post(array $data): array
    {
        return $this->getResponse(
            $this->client->post('tickets.json', ['form_params' => $data,])->getBody()
        );
    }

    public function reply(array $data): array
    {
        if (empty($data['ticketId'])) {
            throw new TeamworkDeskParameterException('The `reply` method expects the passed array param to contain `ticketId`', 400);
        }

        return $this->getResponse(
            $this->client->post(sprintf('tickets/%s.json', $data['ticketId']), ['form_params' => $data,])
                ->getBody()
        );
    }

    public function ticket($ticketId): array
    {
        return $this->getResponse(
            $this->client->get(sprintf('tickets/%s.json', $ticketId))->getBody()
        );
    }

    public function inbox(string $name): array
    {
        $inboxes = $this->getResponse($this->client->get('inboxes.json')->getBody());
        $inbox   = collect($inboxes['inboxes'])->first(fn($inbox) => $inbox['name'] === $name);

        if (!$inbox) {
            throw new TeamworkDeskInboxException("No inbox found with the name: $name!", 400);
        }

        return $inbox;
    }

    public function inboxes(): array
    {
        return $this->getResponse($this->client->get('inboxes.json')->getBody());
    }

    public function me(): array
    {
        return $this->getResponse($this->client->get('me.json')->getBody());
    }

    public function postCustomer(array $data): array
    {
        return $this->getResponse(
            $this->client->put('customers/' . $data['customerId'] . '.json', ['json' => $data,])->getBody()
        );
    }

    public function getFileData(int $id): array
    {
        return $this->getResponse(
            $this->client->get("file/{$id}.json")->getBody()
        );
    }

    public function downloadFile(array $file): array
    {
        $client  = new Client();
        $request = new Request('GET', $file['downloadURL']);

        try {
            $output = $client->send($request)->getBody();

            return [
                'file_data'      => sprintf('data:%s;base64,%s', $file['mimeType'], base64_encode($output)),
                'file_name'      => $file['originalFileName'],
                'mime_type'      => $file['mimeType'],
                'content_length' => strlen($output),
            ];
        } catch (GuzzleException $e) {
            throw new TeamworkDeskHttpException($e->getMessage());
        }
    }

    public function downloadAttachment(int $fileId): array
    {
        $file = $this->getFileData($fileId);
        return $this->downloadFile($file['file']);
    }

    public function upload(int $userId, UploadedFile $file): array
    {
        $filename  = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $path      = sys_get_temp_dir();
        $temp      = $file->move($path, $filename);
        $stream    = fopen($temp->getPathName(), 'rb');

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
        $body     = $response->getBody();
        $body     = $this->getResponse($body);

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
    }

    private function getResponse(StreamInterface $body)
    {
        try {
            return json_decode($body->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new TeamworkDeskJsonException($e->getMessage());
        }
    }
}