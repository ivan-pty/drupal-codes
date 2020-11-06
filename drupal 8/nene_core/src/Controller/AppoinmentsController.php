<?php

namespace Drupal\nene_core\Controller;

/**
 * Defines AppointmentsController class.
 */
class AppointmentsController extends NeneControllerBase {

  /**
   * Get the dates for the students.
   *
   * @return array
   *   An array with the dates.
   */
  protected function getAppointments() {
    return $this->neneApi->getAppointments();
  }

  /**
   * Builds a renderable array for the dates.
   *
   * @return array
   *   A renderable array.
   */
  public function appointmentsContent() {
    return [
      '#theme' => 'nene_appointments',
      '#items' => $this->getAppointments(),
      '#cache' => [
        'contexts' => ['user']
      ]
    ];
  }

}
