<?php

declare(strict_types=1);

/**
 * @file
 * Theme settings form for wiseTalent theme.
 */

use Drupal\Core\Form\FormState;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function wisetalent_form_system_theme_settings_alter(array &$form, FormState $form_state): void {

  $form['wisetalent'] = [
    '#type' => 'details',
    '#title' => t('wiseTalent'),
    '#open' => TRUE,
  ];

  $form['wisetalent']['example'] = [
    '#type' => 'textfield',
    '#title' => t('Example'),
    '#default_value' => theme_get_setting('example'),
  ];

}
