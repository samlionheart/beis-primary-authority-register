<?php

namespace Drupal\par_member_upload_flows\Controller;

use Drupal\par_flows\Controller\ParBaseController;
use Drupal\par_partnership_flows\ParPartnershipFlowsTrait;

/**
 * A controller for displaying the application confirmation.
 */
class ParMemberUploadFlowsFinishController extends ParBaseController {

  use ParPartnershipFlowsTrait;

  public function titleCallback() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function content() {
    // Display the help contact fo this partnership.
    $build['help_text'] = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#value' => '<h1 class="heading-xlarge">Member List Uploaded</h1>',
      '#attributes' => [
        'class' => ['govuk-box-highlight'],
      ],
    ];

    return parent::build($build);
  }

}
