<?php

namespace Drupal\nene_core\Plugin\rest\resource;

use Drupal\nene_core\Services\NeneApi;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Appointments resource
 *
 * @RestResource(
 *   id = "appointments_resource",
 *   label = @Translation("Appointments"),
 *   uri_paths = {
 *     "canonical" = "/api/appointments"
 *   }
 * )
 */
class AppointmentsResource extends ResourceBase {

  /**
   * Custom service for the neneApi.
   *
   * @var \Drupal\nene_core\Services\NeneApi
   */
  protected $neneApi;

  /**
   * Request stack.
   *
   * @var Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs a FileUploadResource instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, $serializer_formats, LoggerInterface $logger, neneApi $nene_api) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->neneApi = $nene_api;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $neneApi = $container->get('nene_core.my_dashboard_api');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $neneApi
    );
  }

  /**
   * Response to GET request.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
    $result = $this->neneApi->getAppointmentsService();
    foreach ($result as $key => $items) {
      foreach ($items as $key_item => $item) {
        if ($key_item == 'month' || $key_item == 'day_text') {
          $result[$key][$key_item] = t($item);
        }
      }
    }
    $response = (!empty($result)) ? $result : '';
    $response = new ResourceResponse($response);
    $response->addCacheableDependency($response);
    return $response;
  }

}
