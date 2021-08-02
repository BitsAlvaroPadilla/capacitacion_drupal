<?php

namespace Drupal\take_a_pet\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TakeBreakController extends ControllerBase{


  public function change_photo(Request $request){
    if ($request->isXmlHttpRequest()){
      $pet_id = $request->request->get('pet_id');
      $selected_pet = Node::load($pet_id);
      $path = file_url_transform_relative(file_create_url($selected_pet->field_image->entity->getFileUri()));
      return new JsonResponse([
        'path' => $path,
        'pet_name' => $selected_pet->label(),
        'pet_age' => $selected_pet->field_number->value,
        'foundation_name' => $selected_pet->field_fundation->entity->label(),
        'foundation_email' => $selected_pet->field_fundation->entity->field_email->value
      ]);
    }
    throw new \Exception('this is not an ajax call');
  }

  public function autocomplete_adopter(Request $request){
    $results = [];
    $input = $request->query->get('q');
    if (!$input) {
      return new JsonResponse($results);
    }
    $input = Xss::filter($input);

    $ids = \Drupal::service('database_operations')->get_adopters($input);
    $nodes = $ids ? Node::loadMultiple($ids) : [];
    foreach ($nodes as $node) {
      $results[] = [
        'value' => EntityAutocomplete::getEntityLabels([$node]),
        'label' => $node->getTitle().' ('.$node->id().')',
      ];
    }
    return new JsonResponse($results);
  }
}
