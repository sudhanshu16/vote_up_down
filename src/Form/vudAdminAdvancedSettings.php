<?php

namespace Drupal\vud\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class vudAdminAdvancedSettings.
 *
 * @package Drupal\vud\Form
 */
class vudAdminAdvancedSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'vud.vudadminadvancedsettings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'vud_admin_advanced_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('vud.vudadminadvancedsettings');
    $form['voting_api_tag'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Voting API tag'),
      '#description' => $this->t('Since Vote Up/Down uses Voting API, all votes will be tagged with this term. (default: vote)&lt;br /&gt;This tag is useful if you have deployed various modules that use Voting API. It should always be a unique value. Usually, there is NO need to change this.'),
      '#default_value' => $config->get('voting_api_tag'),
    ];
    $form['message_on_denied_permission'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Message on denied permission'),
      '#description' => $this->t('When this flag is active, a modal window will be shown to the end user instead of avoid showing the voting links'),
      '#default_value' => $config->get('message_on_denied_permission'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('vud.vudadminadvancedsettings')
      ->set('voting_api_tag', $form_state->getValue('voting_api_tag'))
      ->set('message_on_denied_permission', $form_state->getValue('message_on_denied_permission'))
      ->save();
  }

}
