<?php

namespace Drupal\camp\Form;

use Drupal\Core\Extension\InfoParserInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines form for selecting extra components for the assembler to install.
 */
class FeatureForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'camp_extra_components';
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Extra compoments modules.
   */
  public function buildForm(array $form, FormStateInterface $form_state, array &$install_state = NULL) {
    $form['#title'] = $this->t('Features');

    $form['extra_components_introduction'] = [
      '#weight' => -1,
      '#prefix' => '<p>',
      '#markup' => $this->t("Install additional ready-to-use features in your site."),
      '#suffix' => '</p>',
    ];

    $extraFeatures = $this->featureInfo();

    if(count($extraFeatures) > 0) {

      $form['extra_features'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Camp features'),
        '#tree' => true,
      ];

      foreach ($extraFeatures as $extra_feature_key => $extra_feature_info) {

        $checkbox_title = '';
        $checkbox_description = '';
        $checkbox_selected = FALSE;

        if (isset($extra_feature_info['title'])) {
          $checkbox_title = $extra_feature_info['title'];
        }

        if (isset($extra_feature_info['description'])) {
          $checkbox_description = $extra_feature_info['description'];
        }

        if (isset($extra_feature_info['selected'])) {
          $checkbox_selected = $extra_feature_info['selected'];
        }

        $form['extra_features'][$extra_feature_key] = [
          '#type' => 'checkbox',
          '#title' => $checkbox_title,
          '#description' => $checkbox_description,
          '#default_value' => $checkbox_selected,
        ];

        if (isset($extra_feature_info['config_form']) &&
          $extra_feature_info['config_form'] == TRUE) {
          $form['extra_features'][$extra_feature_key . '_config'] = [
            '#type' => 'fieldset',
            '#title' => $checkbox_title,
            '#states' => [
              'visible' => [
                ':input[name="' . $extra_feature_key . '"]' => ['checked' => TRUE],
              ],
              'invisible' => [
                ':input[name="' . $extra_feature_key . '"]' => ['checked' => FALSE],
              ],
            ],
          ];
        }
      }
    }

    $form['actions'] = [
      'continue' => [
        '#type' => 'submit',
        '#value' => $this->t('Assemble and configure'),
        '#button_type' => 'primary',
      ],
      '#type' => 'actions',
      '#weight' => 5,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('extra_features');
    $modules = array_keys(array_filter($values));
    $GLOBALS['install_state']['camp_features'] = $modules;
  }

  /**
   * @param $modules
   *
   * @return array
   */
  private function camp_only_modules($modules) {
    return array_filter($modules, function($var, $key) {
      return ($var->info['package'] == 'Camp' && !array_key_exists('hidden', $var->info));
    }, ARRAY_FILTER_USE_BOTH);
  }

  /**
   * @return array
   */
  private function featureInfo() {
    $modules = system_rebuild_module_data();
    uasort($modules, 'system_sort_modules_by_info_name');
    $modules = $this->camp_only_modules($modules);

    $camp_modules = [];
    array_walk($modules, function(&$module, $key) use (&$camp_modules){
      $camp_modules[$key] = [
        'title' => $module->info['name'],
        'description' => $module->info['description'],
        'selected' => true
      ];
    });

    return $camp_modules;
  }
}