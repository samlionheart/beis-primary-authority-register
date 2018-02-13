<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\FileInterface;
use Drupal\par_data\Entity\ParDataEntityInterface;
use Drupal\par_data\Entity\ParDataLegalEntity;
use Drupal\par_data\Entity\ParDataOrganisation;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\file\Entity\File;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * The member upload CSV handler.
 */
class ParMemberUploadFlowsConfirmForm extends ParBaseForm {

  use ParPartnershipFlowsTrait;
  use StringTranslationTrait;

  protected $pageTitle = 'Upload Confirm';

  /**
   * The column mappings in the CSV.
   */
  protected $columns = [
    'par_data_premises' => [
      0 => [
        'address' => [
          'address_line1' => 2,
          'address_line2' => 3,
          'address_line3' => 4,
          'locality' => 5,
          'administrative_area' => 6,
          'postal_code' => 7,
        ],
        'nation' => 8,
      ],
    ],
    'par_data_person' => [
      0 => [
        'first_name' => 9,
        'last_name' => 10,
        'work_phone' => 11,
        'mobile_phone' => 12,
        'email' => 13,
      ],
    ],
    'par_data_legal_entity' => [
      0 => [
        'registered_name' => 16,
        'legal_entity_type' => 17,
        'registered_number' => 18,
      ],
      1 => [
        'registered_name' => 19,
        'legal_entity_type' => 20,
        'registered_number' => 21,
      ],
      2 => [
        'registered_name' => 22,
        'legal_entity_type' => 23,
        'registered_number' => 24,
      ],
    ],
    'par_data_organisation' => [
      0 => [
        'organisation_name' => 1,
        'nation' => 8,
      ],
    ],
    'par_data_coordinated_business' => [
      0 => [
        'date_membership_began' => 14,
        'date_membership_ceased' => 15,
      ],
    ],
  ];

  /**
   * Defaultl values with need to be saved.
   */
  protected $defaults = [
    'par_data_premises' => [
      'address' => [
        'country_code' => 'GB',
      ],
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'par_partnership_member_csv_confirm';
  }

  protected function getColumns() {
    return $this->columns;
  }

  public function getColumn($entity_type, $field_name, $i, $property = NULL) {
    $columns = $this->getColumns();

    if ($property) {
      return isset($columns[$entity_type][$i][$field_name][$property]) ? $columns[$entity_type][$i][$field_name][$property] - 1 : NULL;
    }
    else {
      return isset($columns[$entity_type][$i][$field_name]) ? $columns[$entity_type][$i][$field_name] - 1 : NULL;
    }
  }

  protected function getDefaults() {
    return $this->defaults;
  }

  /**
   * Getting for accessing the values from the CSV row.
   *
   * @param $row
   * @param $column
   *
   * @return string
   *   The value at the given index, a default value, or NULL
   */
  public function getRowValue($row, $column) {
    if (isset($row[$column]) && !empty($row[$column])) {
      return $row[$column];
    }
    else {
      return NULL;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $members = $this->getFlowDataHandler()->getTempDataValue("coordinated_members", $cid);

    kint($this->getFlowDataHandler()->getParameter('par_data_partnership'));

    kint($this->getFlowDataHandler()->getAllTempData());
    kint($members);

    if (!empty($members)) {
      $count = count($members);
      $form['member_summary'] = [
        '#type' => 'markup',
        '#markup' => $this->formatPlural($count,
          '@count member has been found and is ready to be imported.',
          '@count members have been found and are ready to be imported.',
          ['%count' => $count]
        ),
        '#prefix' => '<p>',
        '#suffix' => '</p>',
      ];
    }
    else {
      $form['members']['summary'] = [
        '#type' => 'markup',
        '#description' => 'No members could be found in your CSV, please check the format is correct or contact the helpdesk for further assistance.',
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // Get the *full* partnership entity from the URL.
    $route_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');

    $cid = $this->getFlowNegotiator()->getFormKey('par_member_upload_csv');
    $members = $this->getFlowDataHandler()->getTempDataValue("coordinated_members", $cid);

    foreach ($members as $i => $member) {
      // Process the row.
      $this->processRow($route_partnership->id(), $member);
    }

  }

  /**
   * Helper to process the raw CSV row data and map to entity values.
   *
   * @param $par_data_partnership_id
   *   The id of the partnership that this member is being attached to.
   * @param $member
   *   The raw CSV row data.
   */
  public function processRow($par_data_partnership_id, $member) {
    $data = [];
    foreach ($this->getColumns() as $entity_type => $entities_data) {
      $data[$entity_type] = [];

      // Ignore the column index values.
      foreach ($entities_data as $entity_key => $entity_data) {
        foreach ($entity_data as $field => $properties) {
          if (is_array($properties)) {
            $data[$entity_type][$entity_key][$field] = [];
            foreach ($properties as $property => $index) {
              $column = $this->getColumn($entity_type, $field, $entity_key, $property);
              $data[$entity_type][$entity_key][$field][$property] = $this->getRowValue($member, $column);
            }
          }
          else {
            $column = $this->getColumn($entity_type, $field, $entity_key);
            $data[$entity_type][$entity_key][$field] = $this->getRowValue($member, $column);
          }
        }
      }
    }

    // Make sure we set the default values
    $data = $data + $this->getDefaults();

    // Send all the data off to the queue for processing.
    $this->addRowToQueue($par_data_partnership_id, $data);
  }

  /**
   * Helper to add the member to the queue to process intensive operations in the background.
   *
   * @param $par_data_partnership
   *   The partnership that this member is being attached to.
   * @param $member
   *   The raw CSV row data.
   * @param null $existing
   *   Whether the organisation being added exists or needs to be created.
   */
  public function addRowToQueue($par_data_partnership_id, $member) {
    // Generate the appropriate data array for passing to the queue.
    $data = [
      'partnership' => $par_data_partnership_id,
      'row' => $member,
    ];

    try {
      $queue = \Drupal::queue('par_partnership_add_members', TRUE);
      $queue->createQueue();
      $queue->createItem($data);

      // Remove all coordinated businesses from the partnership.
      $par_data_partnership = $this->getFlowDataHandler()->getParameter('par_data_partnership');
      $par_data_partnership->set('field_coordinated_business', NULL);
      $par_data_partnership->save();
    } catch (\Exception $e) {
      // @TODO Log this in a way that errors can be reported to the uploader.
    }

    drupal_set_message(t('The members are being processed, please check back shortly to see the membership list updated.'), 'status');
  }

}
