<?php

namespace Drupal\take_a_pet\Plugin\Block;

/**
 * Provides a pet info block
 * @Block (
 *   id = "pet_info_block",
 *   admin_label = @Translation("Pet info Block")
 * )
 */

use Drupal\Core\Block\BlockBase;

class PetInfoBlock extends BlockBase{


  public function build()
  {
    $path = "https://pixabay.com/photos/2018/04/27/19/11/anonumous-3355586_960_728.png";
    return [
        '#markup' => "<div id='pet_info'>
                        <img id='pet_image' src='$path'>
                        <h1 id='pet_name'>Select a pet</h1>
                        <h2 id='pet_age'></h2>
                        <h2 id='foundation_name'></h2>
                        <h2 id='foundation_email'></h2>
                      <div>"
    ];
  }
}
