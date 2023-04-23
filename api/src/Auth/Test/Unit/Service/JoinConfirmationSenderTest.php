<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Services\JoinConfirmationSender;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;

/**
 * @psalm-suppress UnusedClass
 */
#[CoversClass(JoinConfirmationSender::class)]
class JoinConfirmationSenderTest extends TestCase
{
    #[Test]
    public function success(): void
    {
        $from = ['test@app.test' => 'Test'];
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new \DateTimeImmutable());
        $confirmUrl = '/join/confirm?token=' . $token->getValue();

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send')
            ->willReturnCallback(static function (MimeEmail $message) use ($from, $to, $confirmUrl): void {
                foreach ($message->getFrom() as $fromAddress) {
                    self::assertTrue(isset($from[$fromAddress->getAddress()]));
                    self::assertEquals($from[$fromAddress->getAddress()], $fromAddress->getName());
                }

                foreach ($message->getTo() as $toAddress) {
                    self::assertEquals($to->getValue(), $toAddress->getAddress());
                }

                self::assertEquals('Join Confirmation', $message->getSubject());

                self::assertStringContainsString($confirmUrl, (string)$message->getHtmlBody());
            });

        $sender = new JoinConfirmationSender($mailer, $from);
        $sender->send($to, $token);
    }
}
