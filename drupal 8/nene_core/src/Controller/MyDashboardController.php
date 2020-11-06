<?php

namespace Drupal\nene_core\Controller;

/**
 * Defines MyDashboardController class.
 */
class MyDashboardController extends AppointmentsController {

  /**
   * Builds a renderable array for the teacher info.
   *
   * @return array
   *   A renderable array.
   */
  protected function getMyTeacherInfo() {
    $content = $this->neneApi->getTeacherInfoFromUser();
    return [
      '#theme' => 'nene_my_teacher',
      '#items' => $content,
      '#cache' => [
        'contexts' => ['user']
      ]
    ];
  }


  /**
   * Builds a renderable array for the student's dashboard.
   *
   * @return array
   *   A renderable array.
   */
  public function content() {
    $output = [
      'teacher' => $this->getMyTeacherInfo(),
      'appointments' => $this->appointmentsContent(),
    ];
    $build = [
      '#theme' => 'nene_parents_menu',
      '#items' => $output,
      '#attached' => [
        'library' => [
          'nene_core/nene_core.ajaxlinks',
        ],
      ],
      '#cache' => [
        'contexts' => ['user']
      ]
    ];
    return $build;
  }

}
