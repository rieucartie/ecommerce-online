<?php

namespace App\EventSubscriber;

use App\Event\ContactEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContactSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerService
     */
    protected MailerService $mailerService;

   public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    /**
     * @param ContactEvent $event
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
     public function onSendContact(ContactEvent $event)
    {
       $contact = $event->getContact();

        $parameters = [
           "email" => $contact->getEmail(),
           "name" => $contact->getName(),
           "description" => $contact->getDescription()
       ];

       /* faire les changements avec les variables d'environements  */

       $this->mailerService->send(
          "mon mail",
           'mail du contact',
           'question',
           ContactEvent::TEMPLATE_CONTACT,
            $parameters
        );
  }

    /**
     * @return array
    */
    public static function getSubscribedEvents()
    {
       return [
           ContactEvent::class => [
                ['onSendContact', 1]
            ]
        ];
   }

}