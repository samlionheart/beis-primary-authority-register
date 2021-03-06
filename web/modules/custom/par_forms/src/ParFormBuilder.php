<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a PAR Form Builder plugin manager.
 *
 * @see \Drupal\Core\Archiver\Annotation\Archiver
 * @see \Drupal\Core\Archiver\ArchiverInterface
 * @see plugin_api
 */
class ParFormBuilder extends DefaultPluginManager {

  use LoggerChannelTrait;
  use StringTranslationTrait;

  /**
   * The logger channel to use.
   */
  const PAR_LOGGER_CHANNEL = 'par';

  /**
   * The logger channel to use.
   */
  const PAR_COMPONENT_PREFIX = 'par_component_';

  /**
   * Constructs a ParScheduleManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/ParForm',
      $namespaces,
      $module_handler,
      'Drupal\par_forms\ParFormPluginInterface',
      'Drupal\par_forms\Annotation\ParForm'
    );

    $this->alterInfo('par_form_info');
    $this->setCacheBackend($cache_backend, 'par_form_info_plugins');
    $this->factory = new DefaultFactory($this->getDiscovery());
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\par_forms\ParFormPluginBase
   */
  public function createInstance($plugin_id, array $configuration = []) {
    $plugin = parent::createInstance($plugin_id, $configuration);

    $plugin->setConfiguration($configuration);

    return $plugin;
  }

  /**
   * Helper to load the data for this plugin.
   *
   * @param $component
   */
  public function loadPluginData($component) {
    // Count the current cardinality.
    $count = $component->countItems() + 1 ?: 1;
    for ($i = 1; $i <= $count; $i++) {
      $component->loadData($i);
    }
  }

  /**
   * Helper to get all the elements from a plugin.
   *
   * @param ParFormPluginBaseInterface $component
   *   The plugin to load elements for.
   * @param array $elements
   *   An array to add the elements to.
   *
   * @return array
   */
  public function getPluginElements($component, &$elements = []) {
    // Add all the registered components to the form.
    $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginId()] = [
      '#weight' => $component->getWeight(),
      '#tree' => $component->getCardinality() === 1 ? FALSE : TRUE,
    ];

    // Count the current cardinality.
    $count = $component->countItems() + 1 ?: 1;
    for ($i = 1; $i <= $count; $i++) {
      $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginId()][$i-1] = $component->getElements([], $i);

      // Only show remove for plugins with multiple cardinality
      if ($component->getCardinality() !== 1) {
        $elements[self::PAR_COMPONENT_PREFIX . $component->getPluginId()][$i-1]['remove'] = [
          '#type' => 'submit',
          '#name' => "remove:{$component->getPluginId()}:{$i}",
          '#weight' => 100,
          '#submit' => ['::removeItem'],
          '#value' => $this->t("Remove"),
          '#attributes' => [
            'class' => ['btn-link'],
          ],
        ];
      }
    }

    if ($component->getCardinality() === -1 || ($component->getCardinality() > 1 && $component->getCardinality() > $count)) {
      $elements['actions']['add_another'] = [
        '#type' => 'submit',
        '#name' => 'add_another',
        '#validate' => ['::validateForm'],
        '#submit' => ['::multipleItemActionsSubmit'],
        '#value' => $this->t('Add another'),
        '#attributes' => [
          'class' => ['btn-link'],
        ],
      ];
    }

    return $elements;
  }

  public function validatePluginElements($component, $form_state) {
    $violations = [];

    // Count the current cardinality.
    $count = $component->countItems() + 1 ?: 1;
    for ($i = 1; $i <= $count; $i++) {
      $violations[$component->getPluginId()][$i] = $component->validate($form_state, $i);
    }

    return $violations;
  }

}
