<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataAdvice;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;

use Drupal\file\Entity\File;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The advice document form.
 */
class ParPartnershipFlowsAdviceForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_advice';
  }

  /**
   * {@inheritdoc}
   */
  protected $formItems = [
    'par_data_advice:advice' => [
      'advice_type' => 'advice_type',
      'field_regulatory_function' => 'regulatory_functions',
    ],
  ];

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Partnership being retrieved.
   * @param \Drupal\par_data\Entity\ParDataAdvice $par_data_advice
   *   The advice document being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    if ($par_data_advice) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_advice->id()}");

      // Partnership Confirmation.
      $allowed_types = $par_data_advice->getTypeEntity()->getAllowedValues('advice_type');
      if (!$this->currentUser()->hasPermission('update primary authority advice to local authorities')) {
        unset($allowed_types['authority_advice']);
      }
      $advice_type = $par_data_advice->get('advice_type')->getString();
      if (isset($allowed_types[$advice_type])) {
        $this->loadDataValue('advice_type', $advice_type);
      }

      // Get Regulatory Functions.
      $regulatory_functions = $par_data_advice->get('field_regulatory_function')->referencedEntities();
      $regulatory_options = [];
      foreach ($regulatory_functions as $function) {
        $regulatory_options[$function->id()] = $function->id();
      }
      $this->loadDataValue('regulatory_functions', $regulatory_options);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL, ParDataAdvice $par_data_advice = NULL) {
    $this->retrieveEditableValues($par_data_partnership, $par_data_advice);
    $advice_bundle = $this->getParDataManager()->getParBundleEntity('par_data_advice');

    // Render the document in view mode to allow users to
    // see which one they're confirming details for.
    $document_view_builder = $this->getParDataManager()->getViewBuilder('par_data_advice');

    // Get files from "par_partnership_advice_upload" step.
    $files = $this->getDefaultValues("files", '', 'par_partnership_advice_upload');
    if ($files) {
      // Show files.
      foreach ($files as $file) {
        $file = File::load($file);

        $form['file'][] = [
          '#type' => 'markup',
          '#prefix' => '<p class="file">',
          '#suffix' => '</p>',
          '#markup' => $file->getFileName()
        ];
      }
    }

    $allowed_types = $advice_bundle->getAllowedValues('advice_type');
    if (!$this->currentUser()->hasPermission('update paa2la')) {
      unset($allowed_types['authority_advice']);
    }

    // The advice type.
    $form['advice_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Type of advice'),
      '#options' => $allowed_types,
      '#default_value' => $this->getDefaultValues('advice_type'),
      '#required' => TRUE,
    ];

    // The regulatory functions of the advice entity.
    $form['regulatory_functions'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Regulatory functions this advice covers'),
      '#options' => $this->getParDataManager()->getRegulatoryFunctionsAsOptions(),
      '#default_value' => $this->getDefaultValues('regulatory_functions', []),
    ];

    // Make sure to add the document cacheability data to this form.
    $this->addCacheableDependency($par_data_advice);
    $this->addCacheableDependency($advice_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    if (!array_keys(array_filter($form_state->getValue('regulatory_functions')))) {
      $this->setElementError('regulatory_functions', $form_state, 'Please select at least one regulatory function.');
    };
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get the advice entity from the URL.
    $par_data_advice = $this->getRouteParam('par_data_advice');

    if ($par_data_advice) {
      $allowed_types = $par_data_advice->getTypeEntity()->getAllowedValues('advice_type');
      $advice_type = $this->getTempDataValue('advice_type');

      if (isset($allowed_types[$advice_type])) {
        $par_data_advice->set('advice_type', $advice_type);
      }

      $regulatory_functions_selected = array_keys(array_filter($this->getTempDataValue('regulatory_functions')));
      $par_data_advice->set('field_regulatory_function', $regulatory_functions_selected);

      // Add all the uploaded files from the upload form to the advice and save.
      $files = $this->getTempDataValue('files', 'par_partnership_advice_upload');
      $files_to_add = [];
      foreach ($files as $file) {
        $file = File::load($file);
        if ($file->isTemporary()) {
          $file->setPermanent();
          $file->save();
        }
        $files_to_add[] = $file->id();

      }

      // Check if there are files to add from the Advice Upload form.
      if ($files_to_add) {
        $par_data_advice->set('document', $files_to_add);
      }

      if ($par_data_advice->save()) {
        $this->deleteStore();
      }
      else {
        $message = $this->t('This %advice could not be saved for %form_id');
        $replacements = [
          '%advice' => $par_data_advice->label(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
    else {
      // Get files from "par_partnership_advice_upload" step.
      $files = $this->getDefaultValues('files', '', 'par_partnership_advice_upload');

      $files_to_add = [];

      if ($files) {

        // Loop through files, save to permanent storage.
        foreach ($files as $file) {

          $file = File::load($file);
          $file->setPermanent();
          $file->save();

          $files_to_add[] = $file->id();

        }

      }

      // Create new advice entity.
      $par_data_advice = ParDataAdvice::create([
        'type' => 'advice',
        'uid' => 1,
        'document' => $files_to_add,
      ]);

      // Set advice type.
      $allowed_types = $par_data_advice->getTypeEntity()->getAllowedValues('advice_type');

      $advice_type = $this->getTempDataValue('advice_type');

      if (isset($allowed_types[$advice_type])) {
        $par_data_advice->set('advice_type', $advice_type);
      }

      // Set regulatory functions.
      $regulatory_functions_selected = array_keys(array_filter($this->getTempDataValue('regulatory_functions')));

      $par_data_advice->set('field_regulatory_function', $regulatory_functions_selected);

      // Save new advice entity.
      $par_data_advice->save();

      // Get partnership entity from URL.
      $par_data_partnership = $this->getRouteParam('par_data_partnership');

      // Combine current pieces of advice to prevent overwriting field.
      $partnership_advice = array_merge($par_data_partnership->retrieveEntityIds('field_advice'), [$par_data_advice->id()]);

      // Update field_advice with our new advice ID.
      $par_data_partnership->set('field_advice', $partnership_advice);

      // Save partnership.
      if ($par_data_partnership->save()) {
        $this->deleteStore();
      }
      else {
        $message = $this->t('This %partnership could not be saved for %form_id');
        $replacements = [
          '%partnership' => $par_data_partnership->label(),
          '%form_id' => $this->getFormId(),
        ];
        $this->getLogger($this->getLoggerChannel())->error($message, $replacements);
      }
    }
  }

}
