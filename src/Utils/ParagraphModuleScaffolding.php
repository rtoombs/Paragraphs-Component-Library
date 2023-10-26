<?php

namespace Drupal\paragraphs_component_library\Utils;

class ParagraphModuleScaffolding {

  public static function getModuleScaffolding($paragraph_type) {
    $contents = "
    <?php
    /**
     * Implements hook_theme().
     */
    function pc_{$paragraph_type}_theme(\$existing, \$type, \$theme, \$path): array {
      return [
        'paragraph__$paragraph_type' => [
          'base hook' => 'paragraph',
          'path' => \$path . '/templates',
        ],
      ];
    }";
    return $contents;
  }

  public static function getInfoScaffolding($paragraph_type) {
    $contents = [
      'name' => 'Paragraph Component: ' . $paragraph_type,
      'description' => 'A module for this Paragraph Component',
      'package' => 'Paragraphs',
      'type' => 'module',
      'version' => '1.0',
      'core_version_requirement' => '^9 || ^10',
      'dependencies' => ['paragraphs', 'paragraphs_component_library']
    ];
    return $contents;
  }

  public static function getJsScaffolding($paragraph_type) {
    $cmp = str_replace("_", "", $paragraph_type);
    $contents = "(function ($) {
  $(document).ready(function () {
    console.log('Loaded {$paragraph_type} Component');
  });
})(jQuery);";

    return $contents;
  }

  public static function getCssScaffolding($paragraph_type) {
    $cmp = str_replace("_", "-", $paragraph_type);
    $content = ".paragraph--type--{$cmp} {

}";
    return $content;
  }

  public static function getLibrariesScaffolding($paragraph_type) {
    $cmp = str_replace("_", "-", $paragraph_type);
    $contents = [
      "paragraph--{$cmp}" => [
        'version' => '1.x',
        'css' => [
          'component' => [
            'css/style.css' => [],
          ],
        ],
        'js' => [
          'js/script.js' => [],
        ],
      ],
    ];
    return $contents;
  }

  public static function getPackageScaffolding($paragraph_type) {
    $contents = '
    {
      "name": "' . $paragraph_type . '",
      "version": "1.0.0",
      "description": "",
      "main": "index.js",
      "scripts": {
        "compile": "dart-sass --no-source-map css/style.scss css/style.css"
      },
      "keywords": [],
      "author": "",
      "license": "ISC",
      "devDependencies": {
        "dart-sass": "^1.25.0"
      }
    }';
    return $contents;
  }

}
