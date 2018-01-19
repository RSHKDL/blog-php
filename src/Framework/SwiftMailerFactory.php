<?php
namespace Framework;

use Psr\Container\ContainerInterface;

class SwiftMailerFactory
{


    public function __invoke(ContainerInterface $c): \Swift_Mailer
    {
        if ($c->get('env') === 'production') {
            $transport =  new \Swift_SendmailTransport();
        } else {
            $transport = new \Swift_SmtpTransport($c->get('mail.host'), $c->get('mail.port'), $c->get('mail.security'));
            $transport->setUsername($c->get('mail.username'));
            $transport->setPassword($c->get('mail.password'));
        }
        return new \Swift_Mailer($transport);
    }
}
