<?php

namespace Drupal\camp_redirect\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class MapRedirectSubscriber.
 */
class CampRedirectSubscriber implements EventSubscriberInterface {

  protected $configFactory;

  protected $currentUser;

  /**
   * Constructs a new CampRedirectSubscriber object.
   */
  public function __construct(ConfigFactoryInterface $configFactory,
    AccountProxyInterface $currentUser) {
    $this->configFactory = $configFactory;
    $this->currentUser = $currentUser;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events['kernel.response'] = ['kernel_response'];
    return $events;
  }

  /**
   * This method is called whenever the kernel.response event is
   * dispatched.
   *
   * @param FilterResponseEvent $event
   */
  public function kernel_response(FilterResponseEvent $event) {
    // Only do this for an authenticated user
    if ($this->currentUser->isAuthenticated() &&
      ($this->currentUser->getLastAccessedTime() == 0)){

        // Get the redirect path and the excluded roles from config.
        $path = $this->configFactory->get("camp_redirect.config")->get("donation_path");
        $excluded_roles = $this->configFactory->get("camp_redirect.config")->get("excluded_roles");

        // Compare this user's roles with the excluded roles.
        $current_user_roles = $this->currentUser->getRoles();
        $excluded_role_match = array_intersect($current_user_roles, $excluded_roles);

        if (empty($excluded_role_match)) {
          $response = new RedirectResponse($path);
          $event->setResponse($response);
        }

    }

  }

}
