<?php

namespace Drupal\example_csv_update\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate_source_ui\Form\MigrateSourceUiForm;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;

/**
 * ExampleForm contributes a custom form for the example_csv_update module.
 */
class ExampleForm extends MigrateSourceUiForm {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'example_csv_update_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $options = [];
    foreach ($this->definitions as $definition) {
      $migrationInstance = $this->pluginManagerMigration->createStubMigration($definition);
      if ($migrationInstance->getSourcePlugin() instanceof CSV) {
        $id = $definition['id'];
        $options[$id] = $this->t('%id (supports %file_type)', [
          '%id' => $definition['label'] ?? $id,
          '%file_type' => $this->getFileExtensionSupported($migrationInstance),
        ]);
      }
    }

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Once you have updated your CSV file with the proper translations, upload them here to this form.'),
    ];

    $form['migrations'] = [
      '#type' => 'select',
      '#title' => $this->t('Migrations'),
      '#options' => $options,
    ];
    $form['source_file'] = [
      '#type' => 'file',
      '#title' => $this->t('Upload the source file'),
    ];
    $form['update_existing_records'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Update existing records'),
      '#default_value' => 1,
    ];
    $form['import'] = [
      '#type' => 'submit',
      '#value' => $this->t('Migrate'),
    ];
    return $form;
  }

}
