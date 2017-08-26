<?php

/**
 * @file
 * Theme functions
 */

define('VUD_WIDGET_MESSAGE_ERROR', 0);
define('VUD_WIDGET_MESSAGE_DENIED', 1);

define('VUD_REQUIRED_CTOOLS_API', '2.0-alpha');

/**
 * Load metadata for a single widget without loading all widgets.
 */
function vud_widget_get($name) {
  ctools_include('plugins');
  return ctools_get_plugins('vud', 'widgets', $name);
}

/**
 * Load metadata for all widgets
 */
function vud_widget_get_all() {
  ctools_include('plugins');
  return ctools_get_plugins('vud', 'widgets');
}

/**
 * Load the names of all widgets for use in a select.
 *
 * This can be given directly to #options when choosing a widget.
 */
function vud_widget_get_names() {
  $names = array();
  foreach (vud_widget_get_all() as $name => $plugin) {
    $names[$name] = $plugin['title'];
  }

  asort($names);
  return $names;
}

/**
 * Implementation of hook_theme().
 */
function vud_theme() {

  $module_handler = \Drupal::service('module_handler');
  $module_path = $module_handler->getModule('vud')->getPath();

  return [
    'vud_widget' => [
      'variables' => [
        'id' => NULL,
        'show_links' => TRUE,
        'show_up_as_link' => TRUE,
        'show_down_as_link' => TRUE,
        'show_reset' => TRUE,
        'link_up' => NULL,
        'link_down' => NULL,
        'link_reset' => NULL,
        'class_up' => TRUE,
        'class_down' => TRUE,
        'class_reset' => TRUE,
        'link_class_up' => NULL,
        'link_class_down' => NULL,
        'link_class_reset' => NULL,
        'reset_short_text' => NULL,
        'reset_long_text' => NULL,
        'up_points' => 0,
        'down_points' => 0,
        'unsigned_points' => 0,
        'points' => 0,
        'vote_sum' => 0,
        'widget_template' => 'alternate',
        'base_path' => $module_path,
      ],
    ],
    'vud_votes' => [
      'variables' => [
        'entity_id' => NULL,
        'type' => NULL,
        'tag' => NULL,
        'widget_theme' => NULL,
      ]
    ]
  ];
}

/**
 * Mimic some theming workflow.
 *
 * We do this since we do not have standard theme overwrite because
 * is not possible to decide dinamically the path where the template is
 * located(in contrast with function names and template names).
 *
 * @fixme: Reimplement theme suggestions.
 */
function vud_pseudo_theming($vote_entity, $template_type, $plugin, &$variables) {
  // let modules modify variables passed to the template
  $variables_type = 'vud_' . $template_type . '_template_variables';
  drupal_alter($variables_type, $variables);
  // let widget modify variables passed to the template
  if ($function = ctools_plugin_get_function($plugin, 'alter template variables')) {
    $function($template_type, $variables);
  }

  // @fixme: Remove hardcode.
  $template_file = $plugin['path'] . '/' . $plugin[$template_type . ' template'] . $plugin['extension'];
  return $template_file;
  // and then provide template suggestions by hand
  $suggestions = module_invoke('vud_' . $vote_entity, 'template_suggestions', $template_type, $plugin, $variables['entity_id']);
  if (empty($suggestions)) {
    $template_file = $plugin['path'] . '/' . $plugin[$template_type . ' template'] . $plugin['extension'];
  }
  else {
    global $theme_key;
    $themes = list_themes();
    $current_theme = $themes[$theme_key];
    $paths = array(
      $plugin['path'],
      dirname($current_theme->filename),
    );
    // @fixme: drupal_discover_template() was removed from core.
    //$template_file = drupal_find_theme_templates($paths, $suggestions, $plugin['extension']);
  }

  return $template_file;
}



/**
 * Proxy votes display function, that hook_theme() calls.
 */
function vud_votes_proxy($variables) {
  $plugin = vud_widget_get($variables['widget_theme']);
  if (empty($plugin) || empty($plugin['votes template'])) {
    return;
  }

  $template_file = $plugin['path'] . '/' . $plugin['votes template'] . $plugin['extension'];
  $variables['plugin'] = $plugin;
  $variables['id'] = 'votes-' . $variables['type'] . '-' . $variables['entity_id'];

  $criteria = array(
    'entity_type' => $variables['type'],
    'entity_id' => $variables['entity_id'],
    'value_type' => 'points',
    'tag' => $variables['tag'],
    'function' => 'sum'
  );

  $vote_result = (int) votingapi_select_single_result_value($criteria);
  $variables['unsigned_points'] = $vote_result;

  if ($vote_result > 0) {
    $variables['class'] = 'positive';
    $variables['points'] = '+'. $vote_result;
  }
  else {
    $variables['points'] = $vote_result;
    if ($vote_result < 0) {
      $variables['class'] = 'negative';
    }
    else {
      $variables['class'] = 'neutral';
    }
  }
  $variables['vote_label'] = format_plural(abs($vote_result), 'vote', 'votes');

  vud_add_files('css', $plugin);  // Search and add the CSS files.
  vud_add_files('js', $plugin);   // Search and add the JS files.

  $template_file = vud_pseudo_theming($variables['type'], 'votes', $plugin, $variables);

  // not all widget use votes.tpl.php
  if (function_exists($plugin['render function'])) {
    return $plugin['render function']($template_file, $variables);
  }

  return '';
}

/**
 * Read and load all CSS or JS files in the selected widget directory.
 */
function vud_add_files($type, $plugin) {
  $function = 'drupal_add_' . $type;
  if (empty($plugin[$type])) {
    return;
  }

  if (is_string($plugin[$type])) {
    $css = array($plugin[$type]);
  }

  if (is_array($plugin[$type])) {
    $css = $plugin[$type];
  }

  if (!empty($css)) {
    foreach ($css as $file) {
      $function($plugin['path'] . '/' . $file);
    }
  }
}

/**
 * Function for the main voting handler with Ajax support.
 */
function vud_vote($type, $entity_id, $value, $tag, $widget, $token, $ajax = 'ajax') {
  // If the user is anonymous we don't need to check for a token.
  if (!is_numeric($value) || !drupal_valid_token($token, "vote/$type/$entity_id/$value/$tag/$widget", TRUE)) {
    return MENU_ACCESS_DENIED;
  }

  $vote = array();
  $casted_vote_criteria = array(
    'entity_type' => $type,
    'entity_id' => $entity_id,
    'tag' => $tag,
  ) + votingapi_current_user_identifier();
  $casted_vote = votingapi_select_single_vote_value($casted_vote_criteria);

  // Sanity-check the incoming values.
  if ($value > 0) {
    $value = 1;
  }
  elseif ($value < 0) {
    $value = -1;
  }
  else {
    // Invalid value.
    watchdog('vud', 'Invalid vote on @type @content_id, with value @value, tag @tag and token @token',
      array('@type' => $type, '@content_id' => $content_id, '@value' => $value, '@tag' => $tag, '@token' => $token));
    return;
  }

  $vote['value'] = $value;
  $vote['value_type'] = 'points';
  $tag = $tag ? $tag : variable_get('vud_tag', 'vote');
  $vote['tag'] = $tag;
  $vote['entity_id'] = $entity_id;
  $vote['entity_type'] = $type;
  $votes = array(0 => $vote);
  drupal_alter('vud_votes', $votes);

  // Do not allow to vote with the same value.
  if ($casted_vote == $votes[0]['value']) {
    return;
  }
  votingapi_set_votes($votes);

  if ($ajax == 'ajax') {
    $plugin = vud_widget_get($widget);
    $commands = array();

    if ($function = ctools_plugin_get_function($plugin, 'ajax render')) {
      $commands = $function($type, $entity_id, $value, $tag, $token, $widget);
    }
    else {
      $variables = array(
        'entity_id' =>$entity_id,
        'type' => $type,
        'tag' => $tag,
        'widget_theme' => $widget,
      );
      if (!empty($plugin['widget template'])) {
        $commands[] = ajax_command_replace("#widget-$type-$entity_id", theme('vud_widget', $variables));
      }

      if (!empty($plugin['votes template'])) {
        $commands[] = ajax_command_replace("#votes-$type-$entity_id", theme('vud_votes', $variables));
      }
    }

    return ajax_deliver(array('#type' => 'ajax', '#commands' => $commands));
  }
  else {
    drupal_goto($_SERVER['HTTP_REFERER']);
  }
}

/**
 * Function for the reset handler with Ajax support.
 */
function vud_reset($type, $entity_id, $tag, $widget, $token, $value, $ajax = 'ajax') {
  if (drupal_valid_token($token, "votereset/$type/$entity_id/$tag/$widget", TRUE)) {
    $criteria = array(
      'entity_type' => $type,
      'entity_id' => $entity_id,
      'tag' => $tag,
    ) + votingapi_current_user_identifier();
    votingapi_delete_votes(votingapi_select_votes($criteria));
    votingapi_recalculate_results($type, $entity_id);
  }
  else {
    watchdog('vud',
      'Could not reset votes on @type @entity_id, tag @tag and token @token',
      array('@type' => $type, '@entity_id' => $entity_id, '@tag' => $tag, '@token' => $token));
    drupal_set_message(t('Oops! There was an error resetting your vote!'), 'warning');
  }

  if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    if ($ajax == 'ajax') {
      $plugin = vud_widget_get($widget);
      $commands = array();

      if ($function = ctools_plugin_get_function($plugin, 'ajax render')) {
        $commands = $function($type, $entity_id, $value, $tag, $token, $widget);
      }
      else {
        $variables = array(
          'entity_id' =>$entity_id,
          'type' => $type,
          'tag' => $tag,
          'widget_theme' => $widget,
        );
        if (!empty($plugin['widget template'])) {
          $commands[] = ajax_command_replace("#widget-$type-$entity_id", theme('vud_widget', $variables));
        }

        if (!empty($plugin['votes template'])) {
          $commands[] = ajax_command_replace("#votes-$type-$entity_id", theme('vud_votes', $variables));
        }
      }

      return ajax_deliver(array('#type' => 'ajax', '#commands' => $commands));
    }
  }
  else {
    drupal_goto($_SERVER['HTTP_REFERER']);
  }
}
