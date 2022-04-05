<?php
declare(strict_types=1);

namespace Mailer\Service\Mailer;

use Mailer\Templating\TwigTemplate;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Environment;
use Symfony\Component\Mailer\MailerInterface;

class MailerService{

    private const TEMPLATE_SUBJECT_MAP = [
      TwigTemplate::USER_REGISTER => 'Bienvenid@ª'
    ];
    private MailerInterface $mailer;
    private Environment $engine;
    private LoggerInterface $loggerInterface;
    private string $mailerDefaultSender;

    public function __construct(MailerInterface $mailer,
    Environment $engine, LoggerInterface $loggerInterface,
    string $mailerDefaultSender )
    {
        $this->mailer = $mailer;
        $this->engine = $engine;
        $this->loggerInterface = $loggerInterface;
        $this->mailerDefaultSender = $mailerDefaultSender;   
    }

    /**
     * @throws \Exception
     */
    public function send(string $receiver, string $template, array $payload) : void{
        $email  = (new Email())
                ->from($this->mailerDefaultSender)
                ->to($receiver)
                ->subject(self::TEMPLATE_SUBJECT_MAP[$template])
                ->html($this->engine->render($template,$payload));

        try{
            $this->mailer->send($email);
        }catch(TransportExceptionInterface $e){
            $this->logger->error(\sprintf('Error sending email: %s'), $e->getMessage());
        }
    }
}
