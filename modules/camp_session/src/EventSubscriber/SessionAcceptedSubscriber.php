<?php

/**
 *
 */
namespace Drupal\camp_session\EventSubscriber;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\flag\Event\FlagEvents;
use Drupal\flag\Event\FlaggingEvent;
use Drupal\flag\Event\UnflaggingEvent;

/**
 * Class SessionAcceptedSubscriber
 *
 * @package Drupal\camp_session\EventSubscriber
 */
class SessionAcceptedSubscriber implements EventSubscriberInterface {

  /** @var \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannel */
  protected $loggerChannel;

  /** @var \Drupal\core\Mail\MailManagerInterface $mail */
  protected $mail;

  /** @var \Drupal\Core\Session\AccountProxyInterface $account */
  protected $account;

  /** @var \Drupal\Core\Entity\EntityManagerInterface $entityManager */
  protected $entityManager;

  /**
   * SessionAcceptedSubscriber constructor.
   *
   * @param \Drupal\Core\Mail\MailInterface $mail
   */
  public function __construct(LoggerChannelFactoryInterface $loggerChannel, MailManagerInterface $mail, AccountProxyInterface $account, EntityManagerInterface $entityManager) {
    $this->loggerChannel = $loggerChannel;
    $this->mail = $mail;
    $this->account = $account;
    $this->entityManager = $entityManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    //$events[FlagEvents::ENTITY_FLAGGED][] = ['notifySessionPresenters'];
    //$events[FlagEvents::ENTITY_UNFLAGGED][] = ['notifySessionPresenterRejected'];
    return [];
    //return $events;
  }

  /**
   * @param \Drupal\flag\Event\FlaggingEvent $event
   */
  public function notifySessionPresenters(FlaggingEvent $event) {
    $id = $event->getFlagging()->get('entity_id')->getString();
    $type = $event->getFlagging()->get('entity_type')->getString();
    if($type == 'node' && false){
      $entity = $this->entityManager->getStorage('node')->load($id);
      if($entity->bundle() != 'session')
        return;

      $this->sendEmail('session_accepted', $entity);
    }
  }

  /**
   * @param \Drupal\flag\Event\FlaggingEvent $event
   */
  function notifySessionPresenterRejected(UnflaggingEvent $event) {
    $id = $event->getFlagging()->get('entity_id')->getString();
    $type = $event->getFlagging()->get('entity_type')->getString();
    if($type == 'node' && false){
      $entity = $this->entityManager->getStorage('node')->load($id);
      if($entity->bundle() != 'session')
        return;

      $this->sendEmail('session_rejected', $entity);
    }

  }

  private function sendEmail($key, $entity){
    return true;
    $module = 'camp_session';
    $to = $this->account->getEmail();
    $langcode = $this->account->getPreferredLangcode();
    $send = true;
    $params['entity'] = $entity;

    $result = $this->mail->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] != true) {
      $message = t('There was a problem sending your email notification to @email.', array('@email' => $to));
      drupal_set_message($message, 'error');
      $this->loggerChannel->get('mail-log')->error($message);
      return;
    }

    $message = t('An email notification has been sent to @email ', array('@email' => $to));
    drupal_set_message($message);
    $this->loggerChannel->get('mail-log')->notice($message);

  }

}