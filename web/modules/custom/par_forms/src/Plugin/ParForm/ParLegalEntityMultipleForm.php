<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * About multiple legal entities form.
 *
 * @ParForm(
 *   id = "legal_entity_add_multiple",
 *   title = @Translation("Legal entity form.")
 * )
 */
class ParLegalEntityMultipleForm extends ParFormPluginBase {

  public function loadData() {
    if ($par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership')) {
      $partnership_legal_entities = $par_data_partnership->getLegalEntity();

      kint($partnership_legal_entities);

      if (!empty($partnership_legal_entities)) {
        foreach ($partnership_legal_entities as $legal_entity) {
          $data[] = [
            'registered_name' => $legal_entity->get('registered_name')->getString(),
            'registered_number' => $legal_entity->get('registered_number')->getString(),
            'legal_entity_type' => $legal_entity->get('legal_entity_type')->getString(),
          ];
        }

//        $this->getFlowDataHandler()->setTempDataValue('legal_entity', $data);
      }
    }

    parent::loadData(); // TODO: Change the autogenerated stub
  }

  public function getLegalEntityFormElements($i = 1) {
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');

    $default_values = $this->getFlowDataHandler()
      ->getTempDataValue("legal_entity");

    // Prevent any unavailable default values due to adding empty fields.
    if (!isset($default_values[$i])) {
      $default_values[$i] = [
        'registered_name' => NULL,
        'legal_entity_type' => NULL,
        'registered_number' => NULL,
      ];
    }

    return [
      '#type' => 'fieldset',
      '#title' => $this->t('Legal Entity @index', ['@index' => $i]),
      'registered_name' => [
        '#type' => 'textfield',
        '#title' => $this->t('Enter name of the legal entity'),
        '#default_value' => $default_values[$i]["registered_name"],
      ],
      'legal_entity_type' => [
        '#type' => 'select',
        '#title' => $this->t('Select type of Legal Entity'),
        '#default_value' => $default_values[$i]["legal_entity_type"],
        '#options' => $legal_entity_bundle->getAllowedValues('legal_entity_type'),
      ],
      'registered_number' => [
        '#type' => 'textfield',
        '#title' => $this->t('Provide the registration number'),
        '#default_value' => $default_values[$i]["registered_number"],
        '#states' => [
          'visible' => [
            'select[name="legal_entity[' . $i . '][legal_entity_type]"]' => [
              ['value' => 'limited_company'],
              ['value' => 'public_limited_company'],
              ['value' => 'limited_liability_partnership'],
              ['value' => 'registered_charity'],
              ['value' => 'partnership'],
              ['value' => 'limited_partnership'],
              ['value' => 'other'],
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getElements($form = []) {
    $fields_to_display = $this->getFlowDataHandler()
      ->getDefaultValues('fields_to_display', 1);

    // Hidden field to persist between reloads.
    $form['fields_to_display'] = [
      '#type' => 'hidden',
      '#default_value' => $fields_to_display,
    ];

    $form['legal_entity_intro'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What is a legal entity?'),
      'text' => [
        '#type' => 'markup',
        '#markup' => "<p>" . $this->t("A legal entity is any kind of individual or organisation that has legal standing. This can include a limited company or partnership, as well as other types of organisations such as trusts and charities.") . "</p>",
      ],
    ];

    // Configure legal entity form to work nicely with multiple values.
    $form['legal_entity'] = [
      '#tree' => TRUE,
    ];

    for ($i = 1; $i <= $fields_to_display; $i++) {
      // Add form elements.
      $form['legal_entity'][$i] = $this->getLegalEntityFormElements($i);
    }

    // "Add another legal entity" submit button (styled like a link).
    $form['actions']['add_another'] = [
      '#type' => 'submit',
      '#name' => 'add_another',
      '#submit' => ['::addAnotherItemSubmit'],
      '#value' => $this->t('Add Another Legal Entity'),
      '#attributes' => [
        'class' => ['btn-link'],
      ],
    ];

    return $form;
  }
}
