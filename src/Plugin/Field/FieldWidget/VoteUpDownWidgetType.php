<?php

namespace Drupal\vud\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'vote_up_down_widget_type' widget.
 *
 * @FieldWidget(
 *   id = "vote_up_down_widget_type",
 *   label = @Translation("Vote up down widget type"),
 *   field_types = {
 *     "vote_up_down_field"
 *   }
 * )
 */
class VoteUpDownWidgetType extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 60,
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['size'] = [
      '#type' => 'number',
      '#title' => t('Size of textfield'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 1,
    ];
    $elements['placeholder'] = [
      '#type' => 'textfield',
      '#title' => t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = t('Textfield size: @size', ['@size' => $this->getSetting('size')]);
    if (!empty($this->getSetting('placeholder'))) {
      $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $this->getSetting('placeholder')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $options = array();
    $widgets = \Drupal::service('plugin.manager.vud')->getDefinitions();
    foreach($widgets as $plugin_id => $widget) {
      $options[$plugin_id] = $widget['admin_label'];
    }

    $element += array(
      '#type' => 'fieldset',
    );

    $element['widget_template'] = array(
      '#type' => 'select',
      '#options' => $options,
      '#multiple' => FALSE,
      '#title' => t('My custom field setting'),
      '#default_value' => $value,
    );

    return $element;
  }

}
