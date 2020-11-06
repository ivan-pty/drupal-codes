<?php

namespace Drupal\nene_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form to settings for FCM.
 */
class NeneFcmForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'nene_fcm';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['topic'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Topic'),
      '#description' => $this->t('For Contact, use nene_contact'),
      '#required' => TRUE,
    ];
    $form['body'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Body'),
      '#required' => TRUE,
    ];
    $form['type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Type'),
      '#description' => $this->t('For Contact, use contact'),
      '#required' => TRUE,
    ];
    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send Notification'),
    ];
    return $form;
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
    $vars['topic'][] = $form_state->getValue('topic');
    $vars['body'] = $form_state->getValue('body');
    $vars['type'] = $form_state->getValue('type');
    node_core_evaluate_topics_limit($vars);
  }

}
