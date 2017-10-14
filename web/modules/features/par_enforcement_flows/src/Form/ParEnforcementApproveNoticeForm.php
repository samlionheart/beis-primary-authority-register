<?php

namespace Drupal\par_enforcement_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataEnforcementAction;
use Drupal\par_data\Entity\ParDataEnforcementNotice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

/**
 * The confirmation for creating a new enforcement notice.
 */
class ParEnforcementApproveNoticeForm extends ParBaseForm {

  /**
   * {@inheritdoc}
   */
  protected $flow = 'approve_enforcement';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_enforcement_notice_approve';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   */
  public function retrieveEditableValues(ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {
    if ($par_data_enforcement_notice) {
      $this->setState("approve:{$par_data_enforcement_notice->id()}");

      $allowed_actions = [
        ParDataEnforcementAction::APPROVED,
        ParDataEnforcementAction::BLOCKED,
        ParDataEnforcementAction::REFERRED,
      ];
      foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
        if (in_array($action->getRawStatus(), $allowed_actions)) {
          $this->loadDataValue(['actions', $delta, 'disabled'], TRUE);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataEnforcementNotice $par_data_enforcement_notice = NULL) {
    $this->retrieveEditableValues($par_data_enforcement_notice);

    $form['authority'] = $this->renderSection('Notification of Enforcement action from', $par_data_enforcement_notice, ['field_enforcing_authority' => 'title']);

    if (!$par_data_enforcement_notice->get('field_legal_entity')->isEmpty()) {
      $form['legal_entity'] = $this->renderSection('Regarding', $par_data_enforcement_notice, ['field_legal_entity' => 'title']);

      // @TODO If there is only one organisation for this legal entity
      // we can potentially display the address, but otherwise we
      // can only display the name.
    }
    else {
      $form['legal_entity'] = $this->renderSection('Regarding', $par_data_enforcement_notice, ['legal_entity_name' => 'summary']);
    }

    // Show details of each action.
    if (!$par_data_enforcement_notice->get('field_enforcement_action')->isEmpty()) {
      $form['actions'] = [
        '#type' => 'fieldset',
        '#tree' => TRUE,
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];
    }
    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      $form['actions'][$delta] = [
        '#type' => 'fieldset',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $form['actions'][$delta]['title'] = $this->renderSection('Title of action', $action, ['title' => 'title']);

      $form['actions'][$delta]['regulatory_function'] = $this->renderSection('Regulatory function', $action, ['field_regulatory_function' => 'title']);

      $form['actions'][$delta]['details'] = $this->renderSection('Details', $action, ['details' => 'full']);

      $form['actions'][$delta]['documents'] = $this->renderSection('Attachments', $action, ['document' => 'full']);

      $statuses = [
        ParDataEnforcementAction::APPROVED => 'Allow',
        ParDataEnforcementAction::BLOCKED => 'Block',
      ];
      // This action can only be referred if it has not been referred already.
      if ($action->get('field_action_referral')->isEmpty()) {
        $statuses[ParDataEnforcementAction::REFERRED] = 'Refer';
      }
      $form['actions'][$delta]['primary_authority_status'] = [
        '#type' => 'radios',
        '#title' => $this->t('Review this action'),
        '#options' => $statuses,
        '#default_value' => $this->getDefaultValues(['actions', $delta, 'primary_authority_status'], ParDataEnforcementAction::APPROVED),
        '#disabled' => $this->getDefaultValues(['actions', $delta, 'disabled'], FALSE),
        '#required' => TRUE,
      ];

      $form['actions'][$delta]['primary_authority_notes'] = [
        '#type' => 'textarea',
        '#title' => $this->t('If you plan to block this action you must provide the enforcing authority with a valid reason.'),
        '#default_value' => $this->getDefaultValues("primary_authority_notes"),
        '#disabled' => $this->getDefaultValues(['actions', $delta, 'disabled'], FALSE),
        '#states' => [
          'visible' => [
            ':input[name="actions[' . $delta . '][primary_authority_status]"]' => ['value' => ParDataEnforcementAction::BLOCKED],
          ]
        ],
      ];

      $form['actions'][$delta]['referral_notes'] = [
        '#type' => 'textarea',
        '#title' => $this->t('If you plan to refer this action you must provide the enforcing authority with a valid reason.'),
        '#default_value' => $this->getDefaultValues("referral_notes"),
        '#disabled' => $this->getDefaultValues(['actions', $delta, 'disabled'], FALSE),
        '#states' => [
          'visible' => [
            ':input[name="actions[' . $delta . '][primary_authority_status]"]' => ['value' => ParDataEnforcementAction::REFERRED],
          ]
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $par_data_enforcement_notice = $this->getRouteParam('par_data_enforcement_notice');
    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      $form_data = $form_state->getValue(['actions', $delta], 'par_enforcement_notice_approve');

      // Set an error if an action is not reviewed.
      if (!isset($form_data['primary_authority_status']) || empty($form_data['primary_authority_status'])) {
        $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'Every action in this notice must be reviewed before you can proceed.');
      }

      if ($form_data['primary_authority_status'] == ParDataEnforcementAction::BLOCKED && empty($form_data['primary_authority_notes'])) {
        $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'If you plan to block this action you must provide the enforcing authority with a valid reason.');
      }

      if ($form_data['primary_authority_status'] == ParDataEnforcementAction::REFERRED && empty($form_data['primary_authority_notes'])) {
        $this->setElementError(['actions', $delta, 'referral_notes'], $form_state, 'If you plan to refer this action you must provide the enforcing authority with a valid reason.');
      }

      // Set an error if this action has already been reviewed.
      if ($action->isApproved() || $action->isBlocked() || $action->isReferred()) {
        $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'This action has already been reviewed.');
      }

      // Set an error if it is not possible to change to this status.
      if (!isset($form_data['primary_authority_status']) || !$action->canTransition($form_data['primary_authority_status'])) {
        $this->setElementError(['actions', $delta, 'primary_authority_status'], $form_state, 'This action cannot be changed because it has already been reviewed.');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $par_data_enforcement_notice = $this->getRouteParam('par_data_enforcement_notice');

    foreach ($par_data_enforcement_notice->get('field_enforcement_action')->referencedEntities() as $delta => $action) {
      $form_data = $form_state->getValue(['actions', $delta], 'par_enforcement_notice_approve');

      switch ($form_data['primary_authority_status']) {
        case ParDataEnforcementAction::APPROVED:
          if (!$action->approve()) {
            $message = $this->t('The enforcement notification action entity %entity_id could not be updated to a approved state within %form_id');
            $replacements = [
              '%entity_id' => $action->id(),
              '%form_id' => $this->getFormId(),
            ];
            $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
          }
          break;

        case ParDataEnforcementAction::BLOCKED:
          if (!$action->block($form_data['primary_authority_notes'])) {
            $message = $this->t('The enforcement notification action entity %entity_id could not be updated to a blocked state within %form_id');
            $replacements = [
              '%entity_id' => $action->id(),
              '%form_id' => $this->getFormId(),
            ];
            $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
          }
          break;

        case ParDataEnforcementAction::REFERRED:
          if (!$action->refer($form_data['referral_notes'])) {
            $message = $this->t('The enforcement notification action entity %entity_id could not be updated to a referred state within %form_id');
            $replacements = [
              '%entity_id' => $action->id(),
              '%form_id' => $this->getFormId(),
            ];
            $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
          }
        break;
      }

    }
    $this->deleteStore();
  }
}
