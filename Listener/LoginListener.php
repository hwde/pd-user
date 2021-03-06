<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Pd\UserBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Listener set to user defined Language.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class LoginListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RequestStack
     */
    private $request;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $request)
    {
        $this->entityManager = $entityManager;
        $this->request = $request;
    }

    /**
     * On Login Event.
     *
     * @throws \Exception
     */
    public function onLogin(InteractiveLoginEvent $event): void
    {
        // Get User
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof UserInterface) {
            // Change Site Language to User
            if ($user->getProfile()->getLanguage()) {
                $event->getRequest()->getSession()->set('_locale', $user->getProfile()->getLanguage());
            }

            // Set Last Login
            $user
                ->setLastLogin(new \DateTime())
                ->setLastLoginIp($this->request->getCurrentRequest()->getClientIp());

            // Save
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onLogin',
        ];
    }
}
