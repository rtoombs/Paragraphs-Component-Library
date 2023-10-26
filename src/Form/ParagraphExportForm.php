<?php

namespace Drupal\paragraphs_component_library\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs_component_library\Service\ParagraphBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to trigger the export of a Paragraph Type.
 */
class ParagraphExportForm extends FormBase {

  /**
   * Paragraph Builder Object.
   *
   * @var \Drupal\paragraphs_component_library\Service\ParagraphBuilder
   */
  protected $paragraphBuilder;

  /**
   * Class Constructor.
   *
   * @param \Drupal\paragraphs_component_library\Service\ParagraphBuilder $paragraph_builder
   *   Paragraph Builder Object.
   */
  public function __construct(ParagraphBuilder $paragraph_builder) {
    $this->paragraphBuilder = $paragraph_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('paragraphs_component_library.paragraph_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'paragraph_export_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, EntityInterface $paragraph_type = null) {

    $form['intro'] = [
      '#markup' => "You are about to export the Paragraph Type of <b>$paragraph_type->label</b>.<br><br>
                    By submitting this form a sub-module will be created in <b>Paragraphs Component Library</b> module for this Paragraph Type."
                    ];

    $form_state->set('export_id', $paragraph_type->id());

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
    $paragraph_type = $form_state->get('export_id');
    // Build out module for the designated Paragraph Type.
    $this->paragraphBuilder->buildParagraphComponent($paragraph_type);

    //TODO: Error Checking.
    $this->messenger()->addStatus($this->t('The Export has been completed.'));
  }

}
