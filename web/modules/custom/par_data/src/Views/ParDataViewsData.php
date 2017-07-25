<?php

namespace Drupal\par_data\Views;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for trance entities.
 */
class ParDataViewsData extends EntityViewsData implements EntityViewsDataInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $entity_type_id = $this->entityType->id();
    $entity_type_label = $this->entityType->getLabel();

    $data[$this->entityType->id()]['par_status'] = [
      'field' => [
        'title' => $this->t('PAR Status'),
        'help' => $this->t('Get the PAR status for this entity.'),
        'id' => 'par_data_status',
      ],
    ];

    if (isset($data[$this->entityType->getDataTable()]['table']['base']['title'])) {
      $data[$this->entityType->getDataTable()]['table']['base']['title'] = $entity_type_label;
    }
    if (isset($data[$this->entityType->getRevisionDataTable()]['table']['base']['title'])) {
      $data[$this->entityType->getRevisionDataTable()]['table']['base']['title'] = $this->t('@label revision', [
        '@label' => $entity_type_label,
      ]);
    }

    return $data;
  }

}
