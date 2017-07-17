<?php

namespace Drupal\vud\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\vud\Plugin\VoteUpDownWidgetManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for our example pages.
 */

class VoteUpDownTestController extends ControllerBase{

  protected $voteUpDownWidgetManager;
  
  public function __construct(VoteUpDownWidgetManager $voteUpDownWidgetManager) {
    $this->voteUpDownWidgetManager = $voteUpDownWidgetManager;
  }
  
  public function renderWidget(){
    $build = array();
    
    $vud_widget_definition = $this->voteUpDownWidgetManager->getDefinition('plain');
    
    $build['vud_widget'] = [
      '#theme' => 'vud_widget',
      '#widget_template' => $vud_widget_definition['widgetTemplate']
    ];
  }
  
}