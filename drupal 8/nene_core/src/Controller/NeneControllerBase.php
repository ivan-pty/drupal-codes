<?php

namespace Drupal\nene_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Path\AliasManagerInterface;
use Drupal\nene_core\Services\NeneApi;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines NeneControllerBase
 */
abstract class NeneControllerBase extends ControllerBase {

  /**
   * The alias manager service.
   *
   * @var \Drupal\Core\Path\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Custom service for the neneApi.
   *
   * @var \Drupal\nene_core\Services\NeneApi
   */
  protected $neneApi;

  /**
   * Constructs a new object.
   */
  public function __construct(AliasManagerInterface $alias_manager, EntityTypeManagerInterface $entity_manager, neneApi $nene_api) {
    $this->neneApi = $nene_api;
    $this->aliasManager = $alias_manager;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $neneApi = $container->get('nene_core.my_dashboard_api');
    $aliasManager = $container->get('path.alias_manager');
    $entityManager = $container->get('entity_type.manager');
    return new static($aliasManager, $entityManager, $neneApi);
  }

}
