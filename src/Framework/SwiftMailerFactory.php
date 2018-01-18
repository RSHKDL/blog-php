<?php
namespace Framework;

use Psr\Container\ContainerInterface;

class SwiftMailerFactory
{


    public function __invoke(ContainerInterface $container): \Swift_Mailer
    {
        if ($container->get('env') === 'production') {
            $transport =  new \Swift_SendmailTransport();
        } else {
            $transport = new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl');
            $transport->setUsername('');
            $transport->setPassword('');
        }
        return new \Swift_Mailer($transport);
    }
}
