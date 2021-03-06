<?php

namespace Drupal\par_forms;

use Drupal\Component\Plugin\ConfigurablePluginInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines a form component for use within a form controller.
 *
 * @see plugin_api
 */
interface ParFormPluginBaseInterface extends PluginInspectionInterface, ConfigurablePluginInterface {

  /**
   * Get's the mapping of each form element to the entity
   * property it will be eventually saved on.
   */
  public function getMapping();

  /**
   * Returns all the form elements for this form component.
   *
   * @param array $form
   *   An optional form array to add the component elements to.
   * @param integer $cardinality
   *   The cardinality for this plugin.
   */
  public function getElements($form = [], $cardinality = 0);

  /**
   * Loads the data associated with these elements.
   *
   * @param array $form_state
   *   The form state object to validate.
   * @param integer $cardinality
   *   The cardinality for this plugin.
   */
  public function loadData($cardinality = 0);

  /**
   * Validates the form elements.
   *
   * @param array $form_state
   *   The form state object to validate.
   * @param integer $cardinality
   *   The cardinality for this plugin.
   */
  public function validate(&$form_state, $cardinality = 0);

  /**
   * Saves the form elements.
   *
   * @param array $form_state
   *   The form state object to validate.
   * @param integer $cardinality
   *   The cardinality for this plugin.
   */
  public function save($cardinality = 0);

}
