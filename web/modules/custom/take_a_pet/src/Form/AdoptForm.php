<?php

namespace Drupal\take_a_pet\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Class AdoptForm.
 */
class AdoptForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adopt_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $pets = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'pet', 'field_adopted' => FALSE]);
    $pets_array = [];

    foreach ($pets as $pet){
      $pets_array[$pet->id()] = $pet->label();
    }

    $form['form_adopter'] = [
      '#type' => 'details',
      '#title' => $this->t('Form Adopter'),
      '#descriptiom' => $this->t('Add a new Adoption'),
      '#open' => TRUE
    ];

    $form['form_adopter']['adopter_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Adopter Name'),
      '#autocomplete_route_name' => 'take_a_pet.autocomplete_adopter',
      '#description' => $this->t('Adopter Name'),
      /*'#maxlength' => 255,
      '#size' => 255,
      '#weight' => '0',*/
    ];
    $form['form_adopter']['adopter_phone'] = [
      '#type' => 'number',
      '#title' => $this->t('Adopter Phone'),
      '#description' => $this->t('Adopter Phone'),
      '#weight' => '0',
    ];
    $form['form_adopter']['adoption_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Adoption Date'),
      '#description' => $this->t('Adoption Date'),
      '#weight' => '0',
      '#default_value' => date('Y-m-d')
    ];
    $form['form_adopter']['adopted_pet'] = [
      '#type' => 'select',
      '#title' => $this->t('Adopted Pet'),
      '#options' => $pets_array,
      '#description' => $this->t('Adopted Pet')
    ];
    $form['form_adopter']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $header = [
      [
        'data' => $this->t('Id'),
        'field' => 'id',
        'sort' => 'asc'
      ],
      ['data' => $this->t('Adopter Name'), 'field' => 'adopter_name'],
      ['data' => $this->t('Adopter Phone'), 'field' => 'phone'],
      ['data' => $this->t('Adopter Date'), 'field' => 'adopter_date'],
      ['data' => $this->t('Adopter Pet'), 'field' => 'adopter_pet']
    ];

    $result = $this->getSerachData($header);
    $rows = [];
    foreach ($result as $adoption){
      $url = Url::fromRoute('entity.node.canonical', ['node' => $adoption->adopter_pet]);
      $entity = \Drupal::entityTypeManager()->getStorage('node')->load($adoption->adopter_pet);
      $detail_link = Link::fromTextAndUrl($this->t($entity->label()), $url);
      $detail_link = $detail_link->toRenderable();
      $rows[] = [
          ['data' => $adoption->id],
          ['data' => $adoption->adopter_name],
          ['data' => $adoption->phone],
          ['data' => $adoption->adopter_date],
         // ['data' => $adoption->adopter_pet],
          ['data' => render($detail_link)],
      ];
    }

    $build['config_table'] = [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No adoptions fonud')
    ];

    $form['results'] = $build;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $connection = Database::getConnection();
    $adopted_pet_id = $form_state->getValue('adopted_pet');
    $connection->insert('take_a_pet_adoption')
      ->fields([
          'adopter_name' => $form_state->getValue('adopter_name'),
          'phone' => $form_state->getValue('adopter_phone'),
          'adopter_date' => $form_state->getValue('adoption_date'),
          'adopter_pet' => $form_state->getValue('adopted_pet')
      ])->execute();

    /** @var Node $select_pet */
    $select_pet = Node::load($adopted_pet_id);
    $select_pet->field_adopted->value = TRUE;
    $select_pet->save();

    \Drupal::messenger()->addMessage('Adoption Registered');
  }

  public function getSerachData($header){
    $db = \Drupal::database();
    $query = $db->select('take_a_pet_adoption', 'e');
    $query->fields('e');
    $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')
      ->orderByHeader($header);
    $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
    return $pager->execute();
  }

}
