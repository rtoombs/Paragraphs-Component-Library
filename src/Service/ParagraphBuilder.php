<?php

namespace Drupal\paragraphs_component_library\Service;

use Drupal\Core\Extension\ModuleHandler;
use Drupal\paragraphs_component_library\Service\ParagraphConfigBuilder;
use Symfony\Component\Yaml\Yaml;
use Drupal\paragraphs_component_library\Utils\ParagraphModuleScaffolding;

/**
 * Builds/Scaffolds a submodule for a Paragraph Type.
 */
class ParagraphBuilder {

  /**
   * The Paragraph Config Builder service.
   *
   * @var \Drupal\paragraphs_component_library\Service\ParagraphConfigBuilder
   */
  protected $paragraphConfigBuilder;

  /**
   * The Module Handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * Constructs a ParagraphBuilder object.
   *
   * @param \Drupal\paragraphs_component_library\Service\ParagraphConfigBuilder $paragraph_config_builder
   *   The Paragraph Config Builder service.
   * @param \Drupal\Core\Extension\ModuleHandler $module_handler
   *   The Module Handler service.
   */
  public function __construct(ParagraphConfigBuilder $paragraph_config_builder, ModuleHandler $module_handler) {
    $this->paragraphConfigBuilder = $paragraph_config_builder;
    $this->moduleHandler = $module_handler;
  }

  /**
   * Base method for constructing the module.
   *
   * @param $paragraph_type
   *   The Paragraph Type as a machine name.
   */
  public function buildParagraphComponent($paragraph_type) {
    $base_module_path = $this->moduleHandler->getModule('paragraphs_component_library')->getPath();
    $component_module_path = $base_module_path . '/modules/pc_' . $paragraph_type;

    // Build directories.
    $dir_check = $this->scaffoldDirectories($base_module_path, $paragraph_type);
    if (!$dir_check) {
      return ['error' => 'There was an issue with the module directory'];
    }

    // Build config files.
    $configs = $this->paragraphConfigBuilder->buildConfigs($paragraph_type);
    $config_path = $base_module_path . '/modules/pc_' . $paragraph_type . '/config/optional/';
    $build_config_files = $this->buildConfigFiles($configs, $config_path);
    if (!$build_config_files) {
      return $build_config_files;
    }

    // Build module files.
    $this->buildFiles($component_module_path, $paragraph_type);
  }

  /**
   * Make the directories in the new module.
   *
   * @param $module_path
   *   Path to the paragraph_component_library module
   * @param $paragraph_type
   *   The Paragraph Type as a machine name.
   *
   * @return string[]|true
   *   Return true for success and an array if an error was thrown.
   */
  private function scaffoldDirectories($module_path, $paragraph_type) {
    // Check for 'modules' subdirectory and create if not found.
    $modules_dir_exists = is_dir($module_path . '/modules');
    if (!$modules_dir_exists) {
      $modules_dir = mkdir($module_path . '/modules');
      if (!$modules_dir) {
        return ['error' => 'There was a problem creating the modules directory'];
      }
    }

    // Create the Paragraph Component directory.
    $component_path = $module_path . '/modules/pc_' . $paragraph_type;
    $component_dir = mkdir($component_path);
    if (!$component_dir) {
      return ['error' => 'There was a problem creating the component directory'];
    }

    // Create templates directory.
    $templates_dir = mkdir($component_path . '/templates');
    if (!$templates_dir) {
      return ['error' => 'There was a problem creating the templates directory'];
    }

    // Create config directory
    $config_dir = mkdir($component_path . '/config/optional', 0777, TRUE);
    if (!$config_dir) {
      return ['error' => 'There was a problem creating the config directory'];
    }

    // Make JS/CSS Directories.
    $js_dir = mkdir($component_path . '/js');
    $css_dir = mkdir($component_path . '/css');

    return true;
  }

  /**
   * Create the Paragraph Type config yaml files in the submodule.
   *
   * @param $configs
   *   Config data as key/value pairs in array.
   * @param $config_path
   *   Path to the config files directory in submodule.
   *
   * @return string[]|true
   *   Return true as success and array if error.
   */
  private function buildConfigFiles($configs, $config_path) {
    $status = TRUE;
    foreach ($configs as $key => $data) {

      // Convert config data in array to YAML.
      $yaml = Yaml::dump($data, 2);
      $create_configs = file_put_contents($config_path . $key . '.yml', $yaml);

      if ($create_configs === FALSE) {
        $status = ['error' => 'There was an issues during the config creation process'];
      }
    }
    return $status;
  }

  /**
   * Build the files for the new submodule.
   *
   * @param $component_path
   *   Path to the Paragraph Type submodule.
   * @param $paragraph_type
   *   Paragraph Type machine name.
   *
   * @return void
   */
  private function buildFiles($component_path, $paragraph_type) {
    $paragraphs_dir = $this->moduleHandler->getModule('paragraphs')->getPath();

    //Build .module file.
    $module_scaffolding = ParagraphModuleScaffolding::getModuleScaffolding($paragraph_type);
    file_put_contents($component_path . "/pc_$paragraph_type.module", $module_scaffolding);

    // Build info.yml file.
    $info_scaffolding = ParagraphModuleScaffolding::getInfoScaffolding($paragraph_type);
    $yaml = Yaml::dump($info_scaffolding, 2);
    file_put_contents($component_path . "/pc_$paragraph_type.info.yml", $yaml);

    //Build JS/CSS files.
    $js_scaffolding = ParagraphModuleScaffolding::getJsScaffolding($paragraph_type);
    $css_scaffolding = ParagraphModuleScaffolding::getCssScaffolding($paragraph_type);
    file_put_contents($component_path . "/js/script.js", $js_scaffolding);
    file_put_contents($component_path . "/css/style.scss", $css_scaffolding);

    //Build Libraries file.
    $libraries_scaffolding = ParagraphModuleScaffolding::getLibrariesScaffolding($paragraph_type);
    $yaml = Yaml::dump($libraries_scaffolding, 2);
    file_put_contents($component_path . "/pc_$paragraph_type.libraries.yml", $yaml);

    // Build Template file.
    $component_template_file = 'paragraph--' . str_replace("_", "-", $paragraph_type) . '.html.twig';
    $new_file_path = $component_path . '/templates/'. $component_template_file;
    copy($paragraphs_dir . '/templates/paragraph.html.twig', $new_file_path);

    // Attach library via template file.
    $convert = str_replace("_", "-", $paragraph_type);
    $attach_libraries = "{{ attach_library('pc_$paragraph_type/paragraph--$convert') }}\n";
    file_put_contents($new_file_path, $attach_libraries . file_get_contents($new_file_path));

    // Install SASS.
    $package_scaffolding = ParagraphModuleScaffolding::getPackageScaffolding($paragraph_type);
    file_put_contents($component_path . "/package.json", $package_scaffolding);

  }
}
