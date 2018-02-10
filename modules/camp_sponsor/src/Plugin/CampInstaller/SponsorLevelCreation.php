<?php

namespace Drupal\camp_sponsor\Plugin\CampInstaller;

use Drupal\camp\CampInstallerBase;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Form\FormStateInterface;
use Drupal\camp\Annotation\CampInstaller;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SponsorCreation
 *
 * @package Drupal\camp_sponsor\Plugin\CampInstaller
 * @CampInstaller (
 *   id = "sponsor_level_create",
 *   title = @Translation("Camp Sponsor Levels"),
 *   description = @Translation("Set up sponsor levels quick and easy.")
 * )
 */
class SponsorLevelCreation extends CampInstallerBase {

  /** @var \Drupal\Core\Entity\EntityStorageInterface $term_storage */
  protected $term_storage;

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#markup' => t('The following are levels of sponsorship that are offered.')
    ];

    $form['levels'] = [
      '#type' => 'textarea',
      '#title' => t('Levels'),
      '#description' => t("Add different levels of sponsorships that are offered. Each sponsorship level should be added on it's own line.")
    ];

    return $form;
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement validateForm() method.
  }

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $levels = $form_state->getValue('sponsor_level_create', ['levels' => ''])['levels'];
    $level_array = explode("\r\n", $levels);
    foreach($level_array AS $delta => $level){
      $level = trim($level);
      if($level != ''){
        $this->term_storage->create([
          'name' => $level,
          'vid' => 'sponsor_level',
          'weight' => $delta,
        ])->save();
      }
    }
  }

  /**
   * Constructs a new SystemMenuBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $term_storage
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityStorageInterface $term_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->term_storage = $term_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity.manager')->getStorage('taxonomy_term')
    );
  }

}