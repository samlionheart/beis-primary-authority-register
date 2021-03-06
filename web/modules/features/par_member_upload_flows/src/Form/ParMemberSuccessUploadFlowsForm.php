<?php

namespace Drupal\par_member_upload_flows\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\par_data\Entity\ParDataPartnership;
use Drupal\par_flows\Form\ParBaseForm;
use Drupal\par_member_upload_flows\ParFlowAccessTrait;

/**
 * The upload CSV success page for importing partnerships.
 */
class ParMemberSuccessUploadFlowsForm extends ParBaseForm {

  use ParFlowAccessTrait;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ParDataPartnership $par_data_partnership = NULL) {

    // Upload csv file success message.
    $form['csv_upload_success_message_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('What happens next?'),
      'intro' => [
        '#type' => 'markup',
        '#markup' => '<p>' . $this->t('Your member list will be processed'
          . ' shortly, you will receive an email notification when this is'
          . ' complete.<br /><br />'
          . 'Please allow 10 minutes for members to appear. If there is an'
          . ' error with the processing, the email will guide you on where'
          . ' your files need amending.') . '</p>',
      ]
    ];

    return parent::buildForm($form, $form_state);
  }

}
