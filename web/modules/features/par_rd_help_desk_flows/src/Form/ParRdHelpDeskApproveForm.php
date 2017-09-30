<?php

namespace Drupal\par_rd_help_desk_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * Approving a new partnership.
 */
class ParRdHelpDeskApproveForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_partnership';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_rd_help_desk_approve';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {

    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $par_data_authority = current($par_data_partnership->getAuthority());
    $this->retrieveEditableValues($par_data_partnership);

    $form['partnership_title'] = [
      '#type' => 'markup',
      '#markup' => $this->t('Partnership is approved between'),
      '#prefix' => '<div><h2>',
      '#suffix' => '</h2></div>',
    ];

    $form['partnership_text'] = [
      '#type' => 'markup',
      '#markup' => $par_data_organisation->get('organisation_name')->getString() . ' ' . $par_data_authority->get('authority_name')->getString(),
      '#prefix' => '<div><p>',
      '#suffix' => '</p></div>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }
}