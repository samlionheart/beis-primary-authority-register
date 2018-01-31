<?php

namespace Drupal\par_partnership_confirmation_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * Add multiple legal entities forms.
 */
class ParLegalEntityMultipleForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_legal_entity_add_multiple';
  }

  /**
   * {@inheritdoc}
   */
  public function titleCallback() {
    $legal_entity = $this->getRouteParam('par_data_legal_entity');

    $form_context = $legal_entity ? 'Change the legal entity for your organisation' : 'Add multiple legal entity for your organisation';

    $this->pageTitle = "Update Partnership Information | {$form_context}";

    return parent::titleCallback();
  }

  public function addAnotherItemSubmit(array &$form, FormStateInterface $form_state) {
    $values = $this->cleanseFormDefaults($form_state->getValues());
    $this->getFlowDataHandler()->setFormTempData($values);

    // Get value of current amount of fields displayed.
    $fields_to_display = $this->getFlowDataHandler()
      ->getTempDataValue('fields_to_display');

    // Increment fields to display.
    $fields_to_display++;

    // Populate hidden field to generate more legal entity form elements.
    $this->getFlowDataHandler()->setTempDataValue('fields_to_display', $fields_to_display);
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

//    kint($form_state);

    kint($this->getFlowDataHandler()->getTempDataValue('legal_entity'));

    return parent::buildForm($form, $form_state); // TODO: Change the autogenerated stub
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
  }

}
