<?php

namespace App\EventSubscriber;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use \Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\KernelEvents;

class NewsletterSubscriber implements EventSubscriberInterface
{

    private RequestStack $request;
    private Security $security;

    public function __construct(Security $security, RequestStack $request)
    {
        $this->security = $security;
        $this->request = $request;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {

        $session =  $this->request->getSession();

        if (!$event->isMainRequest() || $event->getRequest()->isXmlHttpRequest()) {
            return false;
        }

        if (null === $this->security->getUser() ) {
            return false;
        }

        if($session->get('nbUserViewPage') > 5) {
            $session->remove('UserPropositionOfNewsletter');
            $session->remove('nbUserViewPage');
        }

        $session->set('nbUserViewPage', $session->get('nbUserViewPage', 0) + 1);

        # Au bout de la quatrième visites de l'utilisateur on lui propose la newsletter...

        if($session->get('nbUserViewPage') === 4) :
            $session->set('UserPropositionOfNewsletter', true);
        endif;

    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $session =  $this->request->getSession();

        if (!$event->isMainRequest() || $event->getRequest()->isXmlHttpRequest()) {
            return;
        }

        if($session->get('nbUserViewPage') === 4) :
            $session->set('UserPropositionOfNewsletter', false);

        endif;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST   => 'onKernelRequest',
            KernelEvents::RESPONSE  => 'onKernelResponse'
        ];
    }
}