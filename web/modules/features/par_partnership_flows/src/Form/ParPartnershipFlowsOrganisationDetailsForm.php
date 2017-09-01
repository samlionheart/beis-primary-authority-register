<?php

namespace Drupal\par_partnership_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The about partnership form for the partnership details steps of the
 * 1st Data Validation/Transition User Journey.
 */
class ParPartnershipFlowsOrganisationDetailsForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_organisation_details';
  }

  /**
   * Helper to get all the editable values when editing or
   * revisiting a previously edited page.
   *
   * @param \Drupal\par_data\Entity\ParDataPartnership $par_data_partnership
   *   The Authority being retrieved.
   */
  public function retrieveEditableValues(ParDataPartnership $par_data_partnership = NULL) {
    if ($par_data_partnership) {
      // If we're editing an entity we should set the state
      // to something other than default to avoid conflicts
      // with existing versions of the same form.
      $this->setState("edit:{$par_data_partnership->id()}");

      // Partnership Information Confirmation.
      $confirmation_value = !empty($par_data_partnership->get('partnership_info_agreed_business')->getString()) ? TRUE : FALSE;
      $this->loadDataValue('confirmation', $confirmation_value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {
    $this->retrieveEditableValues($par_data_partnership);
    // Configuration for each entity is contained within the bundle.
    $partnership_bundle = $this->getParDataManager()->getParBundleEntity('par_data_partnership');
    $person_bundle = $this->getParDataManager()->getParBundleEntity('par_data_person');
    $legal_entity_bundle = $this->getParDataManager()->getParBundleEntity('par_data_legal_entity');
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $par_data_authority = current($par_data_partnership->getAuthority());

    // Organisation summary.
    $par_data_organisation = current($par_data_partnership->getOrganisation());
    $organisation_builder = $this->getParDataManager()->getViewBuilder('par_data_organisation');

    $form['details_intro'] = [
      '#markup' => "Review and confirm the details of your partnership with " . $par_data_authority->authority_name->getString(),
    ];

    $form['business_name'] = [
      '#type' => 'fieldset',
      '#title' => t('Business Name:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['business_name']['name'] = $organisation_builder->view($par_data_organisation, 'title');

    $form['about_business'] = [
      '#type' => 'fieldset',
      '#title' => t('About the business:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $about_organisation = $par_data_organisation ? $organisation_builder->view($par_data_organisation, 'about') : '';
    $form['about_business']['info'] = $this->renderMarkupField($about_organisation);

    $form['about_business']['edit'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('about')->setText('edit')->toString(),
      ]),
    ];

    // Registered address.
    $par_data_premises = $par_data_organisation->getPremises();
    $registered_premises = array_shift($par_data_premises);

    if ($registered_premises) {
      $premises_view_builder = $this->getParDataManager()->getViewBuilder('par_data_premises');

      $form['registered_address']['primary_address'] = [
        '#type' => 'fieldset',
        '#title' => t('Registered address:'),
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $registered_address = $premises_view_builder->view($registered_premises, 'full');
      $form['registered_address']['primary_address']['address'] = $this->renderMarkupField($registered_address);

      $form['registered_address']['primary_address']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('edit_address', [
            'par_data_premises' => $registered_premises->id(),
          ])->setText('edit')->toString(),
        ]),
      ];
    }

    if ($par_data_premises) {

      foreach ($par_data_premises as $premises) {
        $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');

        $form['registered_address'][$premises->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $alternative_person = $person_view_builder->view($premises, 'full');
        $form['registered_address'][$premises->id()]['premises'] = $this->renderMarkupField($alternative_person);

        // We can get a link to a given form step like so.
        $form['registered_address'][$premises->id()]['edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getNextLink('edit_address', [
              'par_data_premises' => $premises->id(),
            ])->setText('edit')->toString(),
          ]),
        ];
      }
    }

    $form['primary_contact_add']['add'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('add_address')->setText('add additional address')->toString(),
      ]),
    ];

    // Contacts.
    // Primary contact summary.
    $par_data_contacts = $par_data_partnership->getOrganisationPeople();
    $par_data_primary_person = array_shift($par_data_contacts);

    $form['primary_contact'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#title' => t('Main business contact:'),
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    if ($par_data_primary_person) {

      $primary_person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');
      $primary_person = $primary_person_view_builder->view($par_data_primary_person, 'summary');
      $form['primary_contact']['details'] = $this->renderMarkupField($primary_person);

      $form['primary_contact']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('edit_contact', [
            'par_data_person' => $par_data_primary_person->id(),
          ])->setText('edit')->toString(),
        ]),
      ];
    }

    if ($par_data_contacts) {
      $form['alternative_people'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      foreach ($par_data_contacts as $person) {
        $person_view_builder = $this->getParDataManager()->getViewBuilder('par_data_person');

        $alternative_person = $person_view_builder->view($person, 'summary');

        $form['alternative_people'][$person->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['alternative_people'][$person->id()]['person'] = $this->renderMarkupField($alternative_person);

        // We can get a link to a given form step like so.
        $form['alternative_people'][$person->id()]['edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getNextLink('edit_contact', [
              'par_data_person' => $person->id(),
            ])->setText('edit')->toString(),
          ]),
        ];
      }
    }

    $form['primary_contact_add']['add'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('add_contact')->setText('add alternative contact')->toString(),
      ]),
    ];

    // Legal Entities.
    $par_data_legal_entities = $par_data_organisation->getLegalEntity();
    $par_data_legal_entity = array_shift($par_data_legal_entities);
    $form['legal_entity'] = [
      '#type' => 'fieldset',
      '#title' => t('Legal Entities:'),
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    if ($par_data_legal_entity) {

      $legal_entity_view_builder = $this->getParDataManager()->getViewBuilder('par_data_legal_entity');
      $legal_entity = $legal_entity_view_builder->view($par_data_legal_entity, 'full');
      $form['legal_entity']['entity'] = $this->renderMarkupField($legal_entity);

      $form['legal_entity']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('edit_legal', [
            'par_data_legal_entity' => $par_data_legal_entity->id(),
          ])->setText('edit')->toString(),
        ]),
      ];
    }

    if ($par_data_legal_entities) {

      foreach ($par_data_legal_entities as $legal_entity_item) {
        $form['legal_entity_' . $legal_entity_item->id()] = [
          '#type' => 'fieldset',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];
        $alternative_legal = $legal_entity_view_builder->view($legal_entity_item, 'full');
        $form['legal_entity_' . $legal_entity_item->id()]['item'] = $this->renderMarkupField($alternative_legal);

        // We can get a link to a given form step like so.
        $form['legal_entity_' . $legal_entity_item->id()][$legal_entity_item->id() . '_edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getNextLink('edit_legal', [
              'par_data_legal_entity' => $legal_entity_item->id(),
            ])->setText('edit')->toString(),
          ]),
        ];
      }
    }

    $form['legal_entity_add'] = [
      '#type' => 'fieldset',
      '#attributes' => ['class' => 'form-group'],
      '#collapsible' => FALSE,
      '#collapsed' => FALSE,
    ];

    $form['legal_entity_add']['add'] = [
      '#type' => 'markup',
      '#markup' => t('@link', [
        '@link' => $this->getFlow()->getNextLink('add_legal')->setText('add another legal entity')->toString(),
      ]),
    ];

    // Trading names.
    $par_data_trading_names = $par_data_organisation->get('trading_name')->getValue();
    if ($par_data_trading_names) {
      $form['trading_names'] = [
        '#type' => 'fieldset',
        '#attributes' => ['class' => 'form-group'],
      ];

      foreach ($par_data_trading_names as $key => $trading_name) {
        $form['trading_names'][$key] = [
          '#type' => 'fieldset',
          '#title' => $key === 0 ? t('Trading Names:') : '',
          '#attributes' => ['class' => 'form-group'],
          '#collapsible' => FALSE,
          '#collapsed' => FALSE,
        ];

        $form['trading_names'][$key]['entity'] = [
          '#type' => 'markup',
          '#markup' => $trading_name['value'],
          '#prefix' => '<div>',
          '#suffix' => '</div>',
        ];

        $form['trading_names'][$key]['edit'] = [
          '#type' => 'markup',
          '#markup' => t('@link', [
            '@link' => $this->getFlow()->getNextLink('edit_trading', [
              'trading_name_delta' => $key,
            ])->setText('edit')->toString(),
          ]),
        ];
      }

      $form['trading_names']['add'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('add_trading')->setText('add another trading name')->toString(),
        ]),
      ];
    }

    if ($this->getFlowName() === 'partnership_direct') {
      $code = 'test';
      $form['sic_code'] = [
        '#type' => 'fieldset',
        '#title' => t('SIC Code:'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $form['sic_code']['code'] = [
        '#plain_text' => $code,
      ];

      $form['sic_code']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('edit_sic', ['par_data_sic_code' => 0])->setText('edit')->toString(),
        ]),
      ];
    }

    if ($this->getFlowName() === 'partnership_coordinated') {
      $size = 'test';
      $form['business_size'] = [
        '#type' => 'fieldset',
        '#title' => t('Number of businesses you represent:'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
      ];

      $form['business_size']['number'] = [
        '#plain_text' => $size,
      ];

      $form['business_size']['edit'] = [
        '#type' => 'markup',
        '#markup' => t('@link', [
          '@link' => $this->getFlow()->getNextLink('size')->setText('edit')->toString(),
        ]),
      ];
    }

    // Need this here so we can add extra checkboxes at the end of the page.
    // We can't guarantee the previous steps will be there.
    $form['confirmation_section'] = [
      '#type' => 'container',
    ];

    $form['confirmation'] = [
      '#type' => 'checkbox',
      '#title' => t('I confirm that the information above is correct.'),
      '#checked' => $this->getDefaultValues('confirmation'),
      '#disabled' => $this->getDefaultValues('confirmation'),
      '#default_value' => $this->getDefaultValues('confirmation'),
      '#return_value' => 'on',
    ];

    $form['next'] = [
      '#type' => 'submit',
      '#name' => 'next',
      '#value' => $this->t('Save'),
    ];

    // Make sure to add the partnership cacheability data to this form.
    $this->addCacheableDependency($par_data_partnership);
    $this->addCacheableDependency($partnership_bundle);
    $this->addCacheableDependency($person_bundle);
    $this->addCacheableDependency($legal_entity_bundle);
    $this->addCacheableDependency($premises_bundle);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // No validation yet.
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Save the value for the about_partnership field.
    $par_data_partnership = $this->getRouteParam('par_data_partnership');

  }

}