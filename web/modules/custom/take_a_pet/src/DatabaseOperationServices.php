<?php

/**
 * @file Providing databases operations
 */

class DatabaseOperationServices{
  public function get_adopters($input){
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'adopter')
      ->condition('title', $input, 'CONTAINS')
      ->groupBy('nid')
      ->sort('created', 'DESC')
      ->range(0, 10);
    return $query->execute();
  }
}
