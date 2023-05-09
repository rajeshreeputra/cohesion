<?php

namespace Drupal\cohesion_elements;

use Drupal\cohesion_elements\Entity\ComponentCategory;
use Drupal\cohesion_elements\Entity\HelperCategory;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\BundlePermissionHandlerTrait;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DynamicPermissions
 * Provides dynamic permissions for categories.
 *
 * @package Drupal\cohesion_elements
 */
class CohesionElementsPermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;
  use BundlePermissionHandlerTrait;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates a ProductMenuLink instance.
   *
   * @param $base_plugin_id
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  public function elementsPermissions() {
    // Generate permissions for all cohesion_element entities.
    $component_categories = $this->entityTypeManager->getStorage('cohesion_component_category')->loadMultiple();
    $cohesion_helper_categories = $this->entityTypeManager->getStorage('cohesion_helper_category')->loadMultiple();
    return array_merge($this->generatePermissions($cohesion_helper_categories, [$this, 'getHelperPermissions']), $this->generatePermissions($component_categories, [$this, 'getCategoryPermissions']));
  }

  /**
   * Returns an array of cohesion_elements category permissions.
   *
   * @return array
   * @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function getCategoryPermissions(ComponentCategory $entity) {
    $permissions = [];
    $permissions += [
      'access ' . $entity->id() . ' '. ComponentCategory::ASSET_GROUP_ID . ' group' => [
        'title' => $this->t('Site Studio Components - @label @type_label category group',
          [
            '@label' => $entity->label(),
            '@type_label' => 'components',
          ]
        ),
        'description' => $this->t('Grant access to the Site Studio @label @type_label category group.',
          [
            '@label' => $entity->label(),
            '@type_label' => 'components',
          ]
        ),
      ],
    ];
    return $permissions;
  }

  /**
   * Returns an array of cohesion_elements helper permissions.
   *
   * @return array
   * @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function getHelperPermissions(HelperCategory $entity) {
    $permissions = [];
    $permissions += [
      'access ' . $entity->id() . ' '. HelperCategory::ASSET_GROUP_ID . ' group' => [
        'title' => $this->t('Site Studio Components - @label @type_label helper group',
          [
            '@label' => $entity->label(),
            '@type_label' => 'helpers',
          ]
        ),
        'description' => $this->t('Grant access to the Site Studio @label @type_label helper group.',
          [
            '@label' => $entity->label(),
            '@type_label' => 'helpers',
          ]
        ),
      ],
    ];
    return $permissions;
  }

}