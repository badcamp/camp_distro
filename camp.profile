<?php

/**
 * @file
 * Enables modules and site configuration for a Camp Distro site installation.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\camp\Form\AssemblerForm;
use Drupal\camp\Form\FeatureForm;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function camp_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {
  // Add a placeholder as example that one can choose an arbitrary site name.
  $form['site_information']['site_name']['#attributes']['placeholder'] = t('Camp Name');
  $form['site_information']['site_name']['#title'] = t('Camp Name');

  $form['#title'] = t('Configure Camp');
}

/**
 * Implements hook_install_tasks().
 */
function camp_install_tasks(&$install_state) {
  return array(
    'camp_features' => array(
      'display_name' => t('Camp Features'),
      'display' => TRUE,
      'type' => 'form',
      'function' => FeatureForm::class,
    ),
    'camp_features_install' => array(
      'display_name' => t('Install Features'),
      'display' => FALSE,
      'type' => 'batch',
    ),
    'camp_assemble_extra_components' => array(
      'display_name' => t('Configure Features'),
      'display' => TRUE,
      'type' => 'form',
      'function' => AssemblerForm::class,
    ),
  );
}

/**
 * Implements hook_install_tasks_alter().
 */
function camp_install_tasks_alter(&$tasks, $install_state) {
  $tasks['install_finished']['function'] = 'camp_after_install_finished';
}

/**
 * Batch job to assemble Camp extra components.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @return array
 *   The batch job definition.
 */
function camp_features_install(array &$install_state) {
  $batch = [
    'title' => t('Installing additional features'),
    'init_message' => t('Starting to build camp features.'),
    'progress_message' => t('Installed Feature @current step of @total.'),
    'error_message' => t('The installation has encountered an error.'),
    'operations' => []
  ];

  array_walk($install_state['camp_features'], function($module) use (&$batch){
    $batch['operations'][] = ['camp_assemble_extra_component_then_install', (array) $module];
  });

  return $batch;
}

/**
 * Camp Distro after install finished.
 *
 * Lanuch auto Camp Tour auto launch after install.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @return array
 *   A renderable array with a redirect header.
 */
function camp_after_install_finished(array &$install_state) {

  global $base_url;

  // After install direction.
  $after_install_direction = $base_url . '/?welcome';

  install_finished($install_state);
  $output = [];

  // Clear all messages.
  drupal_get_messages();

  $output = [
    '#title' => t('Camp Distro'),
    'info' => [
      '#markup' => t('<p>Congratulations, you have installed Camp Distro!</p><p>If you are not redirected to the front page in 5 seconds, Please <a href="@url">click here</a> to proceed to your insalled site.</p>', [
        '@url' => $after_install_direction,
      ]),
    ],
    '#attached' => [
      'http_header' => [
        ['Cache-Control', 'no-cache'],
      ],
    ],
  ];

  $meta_redirect = [
    '#tag' => 'meta',
    '#attributes' => [
      'http-equiv' => 'refresh',
      'content' => '0;url=' . $after_install_direction,
    ],
  ];
  $output['#attached']['html_head'][] = [$meta_redirect, 'meta_redirect'];

  return $output;
}

/**
 * Batch function to assemble and install needed extra components.
 *
 * @param string|array $extra_component
 *   Name of the extra component.
 */
function camp_assemble_extra_component_then_install($extra_component, &$context) {
  try {
    \Drupal::service('module_installer')->install((array) $extra_component, TRUE);
  }
  catch (\Exception $e) {

  }

  $context['results'][] = $extra_component;
  $context['message'] = t('Installed Feature: %module_name', ['%module_name' => $extra_component]);
}