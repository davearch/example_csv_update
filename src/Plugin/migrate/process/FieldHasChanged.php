<?php

namespace Drupal\example_csv_update\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin converts a string to uppercase.
 *
 * @MigrateProcessPlugin(
 *   id = "field_has_changed"
 * )
 */
class FieldHasChanged extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Array with either node or taxonomy id.
    $destination = $row->getRawDestination();
    // Array key 'nid' or 'tid'.
    $id_key = array_keys($destination)[0];
    // The actual id.
    $id = $destination[$id_key];
    $entity_type = ($id_key === 'nid') ? 'node' : 'taxonomy_term';
    // In case of a split destination field such as body/value.
    $field = preg_split("/\//", $destination_property)[0];
    $query = \Drupal::entityQuery($entity_type)
      ->condition($id_key, $id)
      ->condition($field, $value, '=', $row->getSourceProperty('langcode'));
    $result = $query->execute();
    // Field has not changed, return NULL.
    if ($result) {
      return NULL;
    }
    return $value;
  }

}
