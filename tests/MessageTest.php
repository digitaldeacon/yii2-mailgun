<?php
namespace boundstate\mailgun\tests;

use Yii;

final class MessageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockApplication([
            'components' => [
                'mailer' => [
                    'class' => 'boundstate\mailgun\Mailer',
                    'key' => getenv('MAILGUN_KEY'),
                    'domain' => getenv('MAILGUN_DOMAIN'),
                ],
            ]
        ]);
    }

    public function testCompose(): void
    {
        $message = Yii::$app->mailer->compose('example', ['name' => 'John']);
        $html = $message->getMessageBuilder()->getMessage()['html'];

        $this->assertEquals('<p>Hi John!</p>', $html);
    }

    public function testSingleTo(): void
    {
        $message = Yii::$app->mailer->compose('example', ['name' => 'John'])
            ->setTo('a@example.com');
        $this->assertEquals(['a@example.com'], $message->getTo());
    }

    public function testMultiTo(): void
    {
        $message = Yii::$app->mailer->compose('example', ['name' => 'John'])
            ->setTo(['a@example.com', 'b@example.com']);
        $this->assertEquals(['a@example.com', 'b@example.com'], $message->getTo());
    }

    public function testMultiToWithNames(): void
    {
        $message = Yii::$app->mailer->compose('example', ['name' => 'John'])
            ->setTo([
                'a@example.com' => 'Anne',
                'b@example.com' => 'Billy'
            ]);
        $this->assertEquals(['"Anne" <a@example.com>', '"Billy" <b@example.com>'], $message->getTo());
    }

    public function testSend(): void
    {
        $message = Yii::$app->mailer->compose('example', ['name' => 'John'])
            ->setTo(getenv('TEST_RECIPIENT'))
            ->setFrom('test@example.com')
            ->setSubject('Test')
            ->setTestMode(true);

        $this->assertTrue($message->send());
    }
}
