<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Mail;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    protected static $id;

    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/mails');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPost()
    {
        $client = static::createClient();

        $contentArray = [
            "sender" => "test@codibly.loc",
            "status" => "to_send",
            "recipients" => ["test1@codibly.loc", "test2@codibly.loc"],
            "priority" => 1,
            'subject' => 'hello',
            'body' => 'test body'
        ];

        $headers = [
            'CONTENT_TYPE' => "application/json"
        ];

        $client->request('POST', '/mail', [], [], $headers, json_encode($contentArray));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent();
        $decoded = json_decode($content, true);
        $mailId = $decoded['id'] ?? '';
        $this->assertNotEmpty($mailId);
        static::$id = $mailId;
    }

    public function testGet()
    {
        $mailId = static::$id;
        $client = static::createClient();

        $client->request('GET', sprintf('/mail/%s', $mailId));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', sprintf('/mails'));

        $this->assertContains($mailId, $client->getResponse()->getContent());
    }

    public function testSend()
    {
        $mailId = static::$id;
        $client = static::createClient();

        $client->request('GET', sprintf('/send_mails'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', sprintf('/mail/%s', $mailId));

        $content = $client->getResponse()->getContent();
        $decoded = json_decode($content, true);
        $status = $decoded['status'] ?? '';
        $this->assertEquals(Mail::STATUS_SENT, $status);
    }
}
