<?php

namespace Drupal\Tests\vud\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Test the functionality of the vote up/down module.
 *
 * @group vud
 */
class VoteUpDownTest extends BrowserTestBase {
  
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['vud'];
  
  /**
   * The installation profile to use with this test.
   *
   * @var string
   */
  protected $profile = 'standard';
  
  public function testVotingAPIEndpoints() {
    $assert = $this->assertSession();
    
    $definition = \Drupal::entityTypeManager()->getDefinition('node');
    $values = [
      $definition->getKey('bundle') => 'article',
      'title' => 'test_title',
    ];
    $entity = \Drupal::entityTypeManager()->getStorage('node')->create($values);
    
    $entity_id = $entity->getOriginalId();
    $entity_type_id = $entity->getEntityTypeId();
    
    $this->drupalGet('vote/' . $entity_type_id . '/' . $entity_id . '/' . '1');
    $assert->statusCodeEquals(200);
  }
  
}