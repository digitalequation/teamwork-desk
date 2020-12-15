<?php

namespace DigitalEquation\TeamworkDesk\Tests;

use DigitalEquation\TeamworkDesk\Services\TicketService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase;

class TeamworkTestCase extends TestCase
{
    protected $app;

    protected TicketService $tickets;

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        // Setup the Teamwork domain and API Key
        $app['config']->set('teamwork-desk.domain', 'somedomain');
        $app['config']->set('teamwork-desk.key', '04983o4krjwlkhoirtht983uytkjhgkjfh');

        $this->app     = $app;
        $this->tickets = new TicketService();
    }

    /**
     * Build the request for file upload.
     *
     * @param string $fileName
     * @param bool   $multiple
     *
     * @return Request
     */
    protected function getUploadFileRequest(string $fileName, $multiple = false): Request
    {
        Storage::fake('avatars');

        if ($multiple) {
            $files = [
                $fileName => [
                    UploadedFile::fake()->image('image.jpg'),
                    UploadedFile::fake()->image('image2.jpg'),
                ],
            ];
        } else {
            $files = [$fileName => UploadedFile::fake()->image('image.jpg')];
        }

        return new Request(
            [],
            [],
            [],
            [],
            $files,
            ['CONTENT_TYPE' => 'application/json'],
            null
        );
    }

    /**
     * Build the client mock.
     *
     * @param $status
     * @param $body
     *
     * @return Client
     */
    protected function mockClient($status, $body): Client
    {
        $mock    = new MockHandler([new Response($status, [], $body)]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}
