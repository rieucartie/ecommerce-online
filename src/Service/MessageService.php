<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class MessageService
{
    const TYPE_SUCCESS = "success";
    const TYPE_ERROR = "error";


// Injection de dépendances du container
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * @param string $message
     * @return mixed
     */
    public function addSuccess(string $message): void
    {
        $this->container->get(FlashBagInterface::class)->add(self::TYPE_SUCCESS, $message);

    }

    /**
     * @param string $message
     * @return mixed
     */
    public function addError(string $message): void
    {
        $this->container->get(FlashBagInterface::class)->add(self::TYPE_ERROR, $message);
    }
}