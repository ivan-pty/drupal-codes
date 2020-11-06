<?php

namespace Drupal\nene_core\Services;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Path\AliasManagerInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;
use Drupal\views\Views;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\image\Entity\ImageStyle;

/**
 * Class NeneApi.
 */
class NeneApi {

  /**
   * Getting alias information to determine Show slug.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * Determining the current path for getting Show information.
   *
   * @var \Drupal\Core\Path\CurrentPathStack
   */
  protected $currentPath;

  /**
   * For loading entity from alias.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * Getting route object.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Allowing for a custom query for complex filtering.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $dataBase;

  /**
   * Get current User ID.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * PatternEditForm constructor.
   *
   * @param \Drupal\Core\Path\AliasManagerInterface $alias_manager
   *   The alias type manager.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   Determining the current path for getting Show information.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager service.
   * @param \Drupal\Core\E\Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The current route match service.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   Get current User ID.
   * @param \Drupal\Core\Database\Connection $db
   *   Allowing for a custom query for complex filtering.
   */
  public function __construct(
    AliasManagerInterface $alias_manager,
    CurrentPathStack $current_path,
    EntityTypeManager $entity_type_manager,
    CurrentRouteMatch $current_route_match,
    AccountProxyInterface $current_user,
    Connection $db
  ) {
    $this->aliasManager = $alias_manager;
    $this->currentPath = $current_path;
    $this->entityTypeManager = $entity_type_manager;
    $this->currentRouteMatch = $current_route_match;
    $this->currentUser = $current_user;
    $this->dataBase = $db;
  }

  /**
   * Get the profile of the student.
   */
  public function getStudentProfile($uid = NULL) {
    $uid = ($uid) ? $uid : $this->currentUser->id();
    $profile = \Drupal::entityTypeManager()
      ->getStorage('profile')
      ->loadByProperties([
        'uid' => $uid,
        'type' => 'student',
      ]);
    return reset($profile);
  }

  /**
   * Get the teacher profile by his/her uid.
   */
  public function getTeacherProfile($uid = NULL) {
    $uid = ($uid) ? $uid : $this->currentUser->id();
    $profile = \Drupal::entityTypeManager()
      ->getStorage('profile')
      ->loadByProperties([
        'uid' => $uid,
        'type' => 'teacher',
      ]);
    return reset($profile);
  }

  /**
   * Get the headquarter of a classroom.
   */
  public function getHeadquarterByClassroom($class_room) {
    if (empty($class_room)) {
      return;
    }
    $headquarter = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'vid' => 'classroom',
        'tid' => $class_room,
      ]);
    $headquarter = reset($headquarter);
    if (!empty($headquarter->get('field_headquarter')->target_id)) {
      return $headquarter->get('field_headquarter')->target_id;
    }
    return;
  }

  /**
   * Get the student classroom from his/her uid.
   */
  public function getUserClassroom($field = 'tid', $uid = NULL) {
    $profile = $this->getStudentProfile($uid);
    if (!empty($profile) && !empty($profile->get('field_student_class')) && !empty($profile->get('field_student_class')->entity->{$field})) {
      return $profile->get('field_student_class')->entity->{$field}->value;
    }
    return '';
  }

  /**
   * Get the student level from his/her uid.
   */
  public function getUserLevel($field = 'tid', $uid = NULL) {
    $profile = $this->getStudentProfile($uid);
    if (!empty($profile) && !empty($profile->get('field_level')) && !empty($profile->get('field_level')->entity->{$field})) {
      return $profile->get('field_level')->entity->{$field}->value;
    }
    return '';
  }

  /**
   * Get the teacher profile from his/her classroom id.
   */
  public function getTeacherFromClassRoom($classroom) {
    $profile = \Drupal::entityTypeManager()
      ->getStorage('profile')
      ->loadByProperties([
        'field_teacher_class' => $classroom,
        'type' => 'teacher',
      ]);
    return (!empty($profile)) ? reset($profile) : '';
  }

  /**
   * Get the teacher uid from a student of his/her classroom.
   */
  public function getTeacherFromUser($uid = NULL) {
    $class_room = $this->getUserClassroom('tid', $uid);
    if (!empty($class_room)) {
      $teacher_profile = $this->getTeacherFromClassRoom($class_room);
      if (!empty($teacher_profile)) {
        if (!empty($teacher_profile->get('uid'))) {
          return $teacher_profile->get('uid')->target_id;
        }
      }
    }
    return '';
  }

  /**
   * Get the teacher info from a student of his/her classroom.
   */
  public function getTeacherInfoFromUser($uid = NULL, $service = false) {
    $uid = ($uid) ? $uid : $this->currentUser->id();
    $teacher_bio = '';
    $teacher_phone = '';
    $teacher_uid = $this->getTeacherFromUser($uid);
    if (empty($teacher_uid)) {
      return;
    }
    $user = User::load($teacher_uid);
    $user_picture = $this->getTeacherImage($user, $service, false);
    $teacher_profile = $this->getTeacherProfile($teacher_uid);
    if (!empty($teacher_profile->get('field_bio'))) {
      $teacher_bio = $teacher_profile->get('field_bio')->value;
    }
    if (!empty($teacher_profile->get('field_phone'))) {
      $teacher_phone = $teacher_profile->get('field_phone')->value;
    }
    return [
      'name' => $user->field_name->value,
      'last_name' => $user->field_last_name->value,
      'image' => $user_picture,
      'bio' => $teacher_bio,
      'phone' => $teacher_phone,
    ];
  }

  /**
   * Get the image for the teacher.
   */
  public function getTeacherImage($user, $service = false, $local_logo = false) {
    if ($local_logo) {
      return 'local';
    }
    $user_picture = [];
    if (!$user->user_picture->isEmpty() && !in_array('administrator', $user->getRoles())) {
      $user_picture = $user->user_picture->entity->getFileUri();
      if ($service) {
        $user_picture = file_create_url($user_picture);
      } else {
        $user_picture = $user->user_picture->entity->getFileUri();
        $user_picture = [
          '#theme' => 'image_style',
          '#style_name' => 'thumbnail',
          '#uri' => $user_picture,
        ];
      }
    }
    return $user_picture;
  }

  /**
   * Get the dates.
   */
  public function getAppointments($uid = NULL) {
    $output = [];
    $uid = ($uid) ? $uid : $this->currentUser->id();
    $appointments = $this->getAppointmentsByStudent($uid);
    foreach ($appointments as $key => $item) {
      $item->body = strip_tags($item->body);
      $date = strtotime($item->appointment);
      $item->id = $key;
      $item->year = $this->getYearOfTimestamp($date);
      $item->month = $this->getMonthOfTimestamp($date);
      $item->month_phone = t($item->month);
      if (strlen($item->month_phone) > 5) {
        $item->month_phone = t($this->getThreeLettersMonthOfTimestamp($date));
      }
      $item->day_text = $this->getDayTextOfTimestamp($date);
      $item->day_number = $this->getDayNumberOfTimestamp($date);
      $item->time = $this->getTimeOfTimestamp($date);
      $output[$item->nid] = (array)$item;
      unset($output[$item->nid]['nid']);
    }
    usort($output, function($a, $b) {
      return $b['appointment'] <=> $a['appointment'];
    });
    return $output;
  }

  /**
   * Get the image for the teacher(Service).
   */
  public function getTeacherInfoService() {
    $uid = $this->currentUser->id();
    return $this->getTeacherInfoFromUser($uid, true);
  }

  /**
   * Get the dates(Service).
   */
  public function getAppointmentsService() {
    return $this->getAppointments();
  }

  /**
   * Get the topics(Service).
   */
  public function getFcmService() {
    return $this->getFcm();
  }

  /**
   * Get the topics.
   */
  public function getFcm() {
    $uid = $this->currentUser->id();
    $class_room = $this->getUserClassroom('tid', $uid);
    $level = $this->getUserLevel('tid', $uid);
    $headquarter = $this->getHeadquarterByClassroom($class_room);
    return [
      'nene_nene_business',
      'nene_notinene_headquarter_' . $headquarter,
      'nene_notinene_level_' . $level,
      'nene_appointments_classroom_' . $class_room,
      'nene_appointments_uid_' . $uid,
      'nene_contact',
    ];
  }

  /**
   * Get the dates by students.
   */
  public function getAppointmentsByStudent($uid) {
    $query = $this->dataBase->select('node__field_student', 'fe');
    $query->condition('fe.bundle', 'appointments');
    $query->condition('fe.field_student_target_id', $uid);
    $query->join('node_field_data', 'fd', 'fd.nid = fe.entity_id');
    $query->leftJoin('node__body', 'fb', 'fb.entity_id = fd.nid');
    $query->leftJoin('node__field_tutor', 'pe', 'pe.entity_id = fd.nid');
    $query->join('node__field_appointment_date', 'fc', 'fc.entity_id = fd.nid');
    $query->fields('fd', ['title', 'nid']);
    $query->addField('fb', 'body_value', 'body');
    $query->addField('fc', 'field_appointment_date_value', 'appointment');
    $query->addField('pe', 'field_tutor_value', 'tutor');
    $query->orderBy('fc.field_appointment_date_value', 'DESC');
    return $query->execute()->fetchAll();
  }

  /**
   * Retrieves the Month of a timestamp.
   *
   * @return string
   */
  public function getMonthOfTimestamp($time) {
    return date('F', $time);
  }

  /**
   * Retrieves the month(3 letters) of a timestamp.
   *
   * @return string
   */
  public function getThreeLettersMonthOfTimestamp($time) {
    return date('M', $time);
  }

  /**
   * Retrieves the year of a timestamp.
   *
   * @return string
   */
  public function getYearOfTimestamp($time) {
    return date('Y', $time);
  }

  /**
   * Retrieves the day of a timestamp.
   *
   * @return string
   */
  public function getDayTextOfTimestamp($time) {
    return date('l', $time);
  }

  /**
   * Retrieves the time of a timestamp.
   *
   * @return string
   */
  public function getTimeOfTimestamp($time) {
    return date('g:i A', $time);
  }

  /**
   * Retrieves a day number of a timestamp.
   *
   * @return string
   */
  public function getDayNumberOfTimestamp($time) {
    return date('j', $time);
  }

}
