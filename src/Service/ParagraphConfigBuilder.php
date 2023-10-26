<?php

namespace Drupal\paragraphs_component_library\Service;

use Drupal\Core\Config\ConfigManager;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityFieldManager;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Get all the relevant config files for a Paragraph Type.
 */
class ParagraphConfigBuilder {

  /**
   * Config Manager service.
   *
   * @var \Drupal\Core\Config\ConfigManager
   */
  protected $configManager;

  /**
   * Entity Field Manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * Construct a Paragraph Config Builder object.
   *
   * @param \Drupal\Core\Config\ConfigManager $config_manager
   *   Config Manager service.
   * @param \Drupal\Core\Entity\EntityFieldManager $entity_field_manager
   *   Entity Field Manager service.
   */
  public function __construct(ConfigManager $config_manager, EntityFieldManager $entity_field_manager) {
    $this->configManager = $config_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * Base method for collecting the relevant configurations.
   *
   * @param string $paragraph_type
   *   The Paragraph Type machine name.
   * @param $return_objects
   *   Boolean if we want to return the configs as object.
   *
   * @return array
   *   Array of Paragraph Type config data.
   */
  public function buildConfigs(string $paragraph_type, $return_objects = FALSE) {
    $data_array = [];

    $paragraph_configs = $this->getParagraphConfigs($paragraph_type);
    $field_configs = $this->getParagraphFieldConfigs($paragraph_type);

    foreach ($paragraph_configs as $config) {
      $data_array[$config->getName()] = ($return_objects) ? $config : $this->toArray($config);
    }

    foreach ($field_configs as $config) {
      $data_array[$config->id()] = ($return_objects) ? $config : $this->toArray($config);

      $storage_config = $config->getFieldStorageDefinition();
      if ($storage_config) {
        $data_array[$storage_config->id()] = ($return_objects) ? $storage_config : $this->toArray($storage_config);
      }
    }

    return $data_array;
  }

  /**
   * Get configs related to the Paragraph Type.
   *
   * @param string $paragraph_type
   *   Paragraph Type machine name.
   *
   * @return array
   *   Array of configurations for the Paragraph Type.
   */
  public function getParagraphConfigs(string $paragraph_type) {
    $configNames = $this
      ->configManager
      ->getConfigFactory()
      ->listAll('paragraphs.paragraphs_type.' . $paragraph_type);

    $configurations = [];

    foreach ($configNames as $name) {
      $configurations[] = $this->configManager->getConfigFactory()->get($name);
    }

    return $configurations;
  }

  /**
   * Get the Field Configs for the Paragraph Type.
   *
   * @param string $paragraph_type
   *   The Paragraph Type machine name
   *
   * @return array
   *   Array of configs for the Paragraph Type fields.
   */
  public function getParagraphFieldConfigs(string $paragraph_type) {
    $field_definitions = $this
      ->entityFieldManager
      ->getFieldDefinitions('paragraph', $paragraph_type);

    $configurations = [];
    foreach ($field_definitions as $fieldName => $definition) {
      $fieldConfig = FieldConfig::loadByName('paragraph', $paragraph_type, $fieldName);

      if ($fieldConfig) {
        $configurations[] = $fieldConfig;
      }
    }

    return $configurations;
  }

  /**
   * Convert a config object to a key/value array.
   *
   * @param $config
   *   A config object.
   *
   * @return array|mixed[]|null
   */
  private function toArray($config) {

    if ($config instanceof ImmutableConfig) {
      return $config->getRawData();
    }
    else if ($config instanceof FieldConfig || $config instanceof FieldStorageConfig) {
      return $config->toArray();
    }

    return NULL;
  }
}
