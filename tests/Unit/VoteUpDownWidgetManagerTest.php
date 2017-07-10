<?php

namespace Drupal\Tests\vud\Unit;

use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\vud\Plugin\VoteUpDownWidgetManager;

/**
 * Test the manager class of the VoteUpDownWidget
 *
 * @group vud_widget
 */
class VoteUpDownWidgetManagerTest extends UnitTestCase {
  
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
  protected $profile = 'minimal';

  protected $VoteUpDownWidgetManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $cache_backend = $this->prophesize(CacheBackendInterface::class);
    $module_handler = $this->prophesize(ModuleHandlerInterface::class);
    $this->VoteUpDownWidgetManager = new VoteUpDownWidgetManager(new \ArrayObject(), $cache_backend->reveal(), $module_handler->reveal());

    $discovery = $this->prophesize(DiscoveryInterface::class);
    // Create a plugin of the type
    $discovery->getDefinitions()->willReturn([
      'newPlugin' => [
        'plugin_id' => 'new plugin',
        'label' => 'new plugin type',
        'widgetTemplate' => 'plain',
        'voteTemplate' => 'vote'
      ],
    ]);
    // Force the discovery object onto the block manager.
    $property = new \ReflectionProperty(VoteUpDownWidgetManager::class, 'discovery');
    $property->setAccessible(TRUE);
    $property->setValue($this->VoteUpDownWidgetManager, $discovery->reveal());
  }

  /**
   * @covers ::getDefinitions
   */
  public function testDefinitions() {
    $definitions = $this->VoteUpDownWidgetManager->getDefinitions();
    $this->assertSame(['newPlugin'], array_keys($definitions));
  }

}
