<?php

namespace Drupal\camp_schedule\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;

/**
 * @see \Drupal\Core\Form\FormBase
 */
class ContactForm extends FormBase {

  /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
  protected $container;

  /** @var \Drupal\Core\Mail\MailManagerInterface $mailer */
  protected $mailer;

  /** @var \Drupal\Core\Session\AccountProxyInterface $user */
  protected $user;

  /** @var \Drupal\file\FileUsage\FileUsageInterface $fileUsage */
  protected $fileUsage;

  /** @var \Drupal\Core\File\FileSystemInterface $fileSystem */
  protected $fileSystem;

  /**
   * ContactForm constructor.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   * @param \Drupal\Core\Mail\MailManagerInterface $mailer
   * @param \Drupal\Core\Session\AccountProxyInterface $user
   */
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
    $this->mailer = $container->get('plugin.manager.mail');
    $this->user = $container->get('current_user');
    $this->fileUsage = $container->get('file.usage');
    $this->fileSystem = $container->get('file.system');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container);
  }


  /**
   * Build the simple form.
   *
   * A build form method constructs an array that defines how markup and
   * other form elements are included in an HTML form.
   *
   * @param array $form
   *   Default form array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object containing current form state.
   *
   * @return array
   *   The render array defining the elements of the form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $build_info = $form_state->getBuildInfo();
    $args = $build_info['args'];

    if(count($args) == 0 || !($args[0] instanceof NodeInterface)){
      $form_state->setError($form, 'The first argument should be a Node');
      return [];
    }

    $node = $args[0];

    $form['holder'] = [
      '#type' => 'details',
      '#title' => $this->t('Contact Attendees'),
      '#collapsed' => true,
    ];

    $form['holder']['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#default_value' => $this->t('Update Message for %node', ['%node' => $node->getTitle()]),
      '#required' => true,
    ];

    $form['holder']['message'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#required' => true,
    ];

    $form['holder']['attachments'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Attachment'),
      '#description' => $this->t('Include a file in the email.'),
      '#upload_location' => 'public://attachments/',
    ];

    $form['holder']['token_tree'] = [
      '#theme' => 'token_tree_link',
      '#token_types' => array('node', 'user')
    ];

    $form['holder']['actions'] = [
      '#type' => 'actions'
    ];
    $form['holder']['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send Message')
    ];

    return $form;
  }

  /**
   * Getter method for Form ID.
   *
   * @return string
   *   The unique ID of the form defined by this class.
   */
  public function getFormId() {
    return 'form_camp_schedule_contact_form';
  }

  /**
   * Implements form validation.
   *
   * The validateForm method is the default method called to validate input on
   * a form.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Implements a form submit handler.
   *
   * The submitForm method is the default method called for any submit elements.
   *
   * @param array $form
   *   The render array of the currently built form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Object describing the current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $build_info = $form_state->getBuildInfo();
    $args = $build_info['args'];

    /** @var NodeInterface $node */
    $node = $args[0];


    $mailManager = $this->mailer;
    $module = 'camp_schedule';
    $key = 'registrations_contact_email'; // Replace with Your key
    $to = $this->user->getEmail();
    $langcode = $this->user->getPreferredLangcode();
    $send = true;

    $params['node'] = $node;
    $params['user'] = $this->user;

    $params['subject'] = $form_state->getValue('subject');
    $params['message'] = $form_state->getValue('message');

    $attachment = $form_state->getValue('attachment');
    if(count($attachment) > 0) {
      // Load the object of the file by its fid.
      $file = File::load($attachment[0]);
      // Set the status flag permanent of the file object.
      if (!empty($file)) {
        $file->setPermanent();
        // Save the file in the database.
        $file->save();
        $this->fileUsage->add($file, 'node', $node->bundle(), $node->id());
      }

      $params['attachment'] = $this->fileSystem->realpath($file->getFileUri());
    }

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] != true) {
      $message = t('There was a problem sending your email notification to @email.', array('@email' => $to));
      drupal_set_message($message, 'error');
      $this->logger('mail-log')->error($message);
      return;
    }

    $message = t('An email notification has been sent to @email ', array('@email' => $to));
    drupal_set_message($message);
    $this->logger('mail-log')->notice($message);
  }
}