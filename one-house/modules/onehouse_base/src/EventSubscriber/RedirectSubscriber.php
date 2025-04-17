<?php

namespace Drupal\onehouse_base\EventSubscriber;

use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Redirect subscriber class.
 */
class RedirectSubscriber implements EventSubscriberInterface {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack*/
  protected $requestStack;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new RedirectSubscriber.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user instance.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack instance.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match instance.
   */
  public function __construct(AccountInterface $current_user, RequestStack $request_stack, RouteMatchInterface $route_match) {
    $this->currentUser = $current_user;
    $this->requestStack = $request_stack;
    $this->routeMatch = $route_match;
  }

  /**
   * Redirect handling function.
   */
  public function redirectRequest(RequestEvent $event) {
    $newPath = '';

    $currentUser = $this->currentUser;
    $userRoles = $currentUser->getRoles();

    $routeObj = $this->routeMatch->getRouteObject();

    $currentRequest = $this->requestStack->getCurrentRequest();
    $path = $currentRequest->getPathInfo();
    $queries = $currentRequest->query->all();
    $queryStr = $queries['destination'] ?? '';

    if (($path == '/bidder') || str_starts_with($path, '/bidder/')) {
      if ($currentUser->isAuthenticated() && (in_array('administrator', $userRoles) || in_array('bidder', $userRoles))) {
        //
        // Access allowed
        // .
        if (($path == '/bidder') || ($path == '/bidder/')) {
          $newPath = onehouse_base_get_first_menu_url('bidder-main-menu');
        }
      }
      else {
        //
        // Access denied
        // .
        $response = new TrustedRedirectResponse('/system/403');
        $event->setResponse($response);
        return;
      }
    }
    elseif (str_starts_with($routeObj->getPath(), '/user/{user}')) {
      if ($currentUser->isAuthenticated() && in_array('bidder', $userRoles) && !in_array('administrator', $userRoles)) {
        // Redirect to first Bidder menu page.
        $newPath = onehouse_base_get_first_menu_url('bidder-main-menu');
      }
    }

    if ($newPath != '') {
      header('Location: ' . $newPath . $queryStr, TRUE, 301);
      exit();
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['redirectRequest', 30];
    return $events;
  }

}
