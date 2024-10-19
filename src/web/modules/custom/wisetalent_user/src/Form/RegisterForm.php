<?php

declare(strict_types=1);

namespace Drupal\wisetalent_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\UserInterface;

/**
 * Provides a Wisetalent user form.
 */
final class RegisterForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'wisetalent_user_register';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['#prefix'] = '<div class="form-group mb3">';

    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#size' => 60,
      '#maxlength' => UserInterface::USERNAME_MAX_LENGTH,
      '#required' => TRUE,
      '#id'=>'username',
      '#attributes' => [
        'class' => ['form-control'],
        'required' => 'required',
      ],
    ];
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Adresse Email'),
      '#size' => 60,
      '#required' => TRUE,
      '#id'=>'email',
      '#attributes' => [
        'class' => ['form-control'],
        'required' => 'required',
      ],
    ];
    $form['Profils'] = [
      '#type' => 'select',
      '#id'=>'profils',
      '#title' => $this->t('Profils'),
      '#required' => TRUE,
      '#options' => [
        'freelance'=>'Freelance',
        'entreprise'=>'Entreprise',
        'talent'=>'Talent',
      ],
      '#attributes' => [
        'placeholder' => 'Selectioner votre profils' ,
        'class' => ['form-control'],
      ],
    ];
    $form['password'] = [
      '#type' => 'password',
      '#id'=>'password',
      '#title' => $this->t('Password'),
      '#size' => 25,
      '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];
    $form['confirm'] = [
      '#type' => 'password',
      '#title' => $this->t('Confirmer le mot de passe'),
      '#size' => 25,
      '#required' => TRUE,
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];


    $form['actions'] = [
      '#type' => 'actions',
      '#prefix' => '<div class="col-md-12">',
      '#suffix' => '</div>',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => 'Enregister',
      '#attributes'=>[
        'class' => ['site-button']
      ]
    ];
    $form['#suffix'] = '</div>';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    // @todo Validate the form here.
    // Example:
    // @code
    //   if (mb_strlen($form_state->getValue('message')) < 10) {
    //     $form_state->setErrorByName(
    //       'message',
    //       $this->t('Message should be at least 10 characters.'),
    //     );
    //   }
    // @endcode
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->messenger()->addStatus($this->t('The message has been sent.'));
    $form_state->setRedirect('<front>');
  }

}
