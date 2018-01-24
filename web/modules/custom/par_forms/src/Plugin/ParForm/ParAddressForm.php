<?php

namespace Drupal\par_forms\Plugin\ParForm;

use Drupal\par_forms\ParFormPluginBase;

/**
 * Address form plugin.
 *
 * @ParForm(
 *   id = "address",
 *   title = @Translation("Address form.")
 * )
 */
class ParAddressForm extends ParFormPluginBase {

  /**
   * Mapping of the data parameters to the form elements.
   */
  protected $formItems = [
    'par_data_premises:premises' => [
      'address' => [
        'country_code' => 'country_code',
        'address_line1' => 'address_line1',
        'address_line2' => 'address_line2',
        'locality' => 'town_city',
        'postal_code' => 'postcode',
      ],
      'nation' => 'country',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getElements($form = []) {
    $premises_bundle = $this->getParDataManager()->getParBundleEntity('par_data_premises');

    $form['premises_id'] = [
      '#type' => 'hidden',
      '#value' => $this->getFlowDataHandler()->getDefaultValues('premises_id', 'new'),
    ];

    $form['address_line1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter your Address Line 1'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("address_line1"),
    ];

    $form['address_line2'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter your Address Line 2'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("address_line2"),
    ];

    $form['town_city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter your Town / City'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("town_city"),
    ];

    $form['county'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter your County'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("county"),
    ];

    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Select your Nation'),
      '#options' => $premises_bundle->getAllowedValues('nation'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("country"),
    ];

    $form['postcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter your Postcode'),
      '#default_value' => $this->getFlowDataHandler()->getDefaultValues("postcode"),
    ];

    $form['country_code'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Country'),
      '#default_value' => 'GB',
    ];

    return $form;
  }
}