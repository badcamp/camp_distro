<?php

namespace Drupal\Tests\camp_redirect\Unit\EventSubscriber;

use Drupal\camp_redirect\EventSubscriber\CampRedirectSubscriber;
use Drupal\Tests\UnitTestCase;
use Drupal\Core\Session\AccountProxy;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\camp_redirect\EventSubscriber\CampRedirectSubscriber
 * @group camp_redirect
 */
class CampRedirectSubscriberTest extends UnitTestCase {

  /**
   * The mocked config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The mocked AccountProxy.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $acccountProxy;

  /**
   * The mocked event.
   *
   * @var \Symfony\Component\EventDispatcher\Event
   */
  protected $event;

  /**
   * The class we are testing.
   *
   * @var \Drupal\camp_redirect\EventSubscriber\CampRedirectSubscriber
   */
  protected $campRedirectSubscriber;

  /**
   * Create the setup for constants.
   */
  protected function setUp() {
    parent::setUp();

    // Stub config
    $this->configFactory = $this->getConfigFactoryStub([
      'camp_redirect.config' => [
        'donation_path' => '/form/donation',
        'excluded_roles' => [
          'administrator',
        ],
      ],
    ]);

    // Mock AccountProxy
    $this->acccountProxy = $this->prophesize(AccountProxy::CLASS);
    $this->acccountProxy->isAuthenticated()->willReturn(true);
    $this->acccountProxy->getRoles()->willReturn(["authenticated"]);
    $this->acccountProxy->getLastAccessedTime()->willReturn(0);

    // Mock Event
    $this->event = $this->prophesize(FilterResponseEvent::CLASS);

    // Set the class we will be testing.
    $this->campRedirectSubscriber = new CampRedirectSubscriber(
      $this->configFactory,
      $this->acccountProxy->reveal()
    );
  }

  /**
   * Tests the getSubscribedEvents() method.
   */
  public function testGetSubscribedEvents() {
    $this->assertArrayHasKey('kernel.response', $this->campRedirectSubscriber->getSubscribedEvents());
  }

  /**
   * Tests the kernel_response() method.
   */
  public function testKernel_response() {
    // We need to mock the response to a variable since nothing is actually
    // returned from the method.
    $mock_response = '';
    $this->event->setResponse(Argument::type('Symfony\Component\HttpFoundation\RedirectResponse'))
      ->will(function ($args) use (&$mock_response) {
        $mock_response = $args[0];
      });

    /** @var \Symfony\Component\HttpFoundation\RedirectResponse $response */
    $this->campRedirectSubscriber->kernel_response($this->event->reveal());

    $this->assertObjectHasAttribute('headers', $mock_response);
    $this->assertObjectHasAttribute('targetUrl', $mock_response);
    $this->assertAttributeEquals("/form/donation",'targetUrl', $mock_response);

    // Test user role is excluded
    $mock_response = '';
    $this->acccountProxy->getRoles()->willReturn(['administrator']);
    $this->campRedirectSubscriber->kernel_response($this->event->reveal());
    $this->assertEmpty($mock_response);

    // Test if user is not brand new
    $mock_response = '';
    $this->acccountProxy->getRoles()->willReturn(["authenticated"]);
    $this->acccountProxy->getLastAccessedTime()->willReturn(1520371642);
    $this->campRedirectSubscriber->kernel_response($this->event->reveal());
    $this->assertEmpty($mock_response);

  }
}
