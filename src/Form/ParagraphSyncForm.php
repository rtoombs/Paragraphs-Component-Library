<?php

namespace Drupal\paragraphs_component_library\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs_component_library\Service\ParagraphConfigBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configuration form for a popular search entity type.
 */
class ParagraphSyncForm extends FormBase {

  protected $paragraphConfigBuilder;

  public function __construct(ParagraphConfigBuilder $paragraph_config_builder) {
    $this->paragraphConfigBuilder = $paragraph_config_builder;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('paragraphs_component_library.paragraph_export_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paragraph_sync_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, EntityInterface $paragraph_type = null) {

    //TODO: THIS!
    // Functionality is WIP.

    $paragraph_id = $paragraph_type->id();
    $configs = $this->paragraphConfigBuilder->buildConfigs($paragraph_id, TRUE);

    $form['staged']['heading'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => ''
    ];
    foreach ($configs as $id => $config) {

    }

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->messenger()->addStatus($this->t('The configuration has been updated.'));
  }

}
