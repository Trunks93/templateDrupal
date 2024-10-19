<?php


namespace Drupal\wisetalent_user\Controller;

use \Drupal\Core\Controller\ControllerBase;

class UserController extends ControllerBase
{

  /**
   * @return array
   */
  public function login():array
  {
    return [
      "#theme" => "login",
      "#content" => [
        'google_auth_link'=>'link'
      ]
    ];
  }
  /**
   * @return array
   */
  public function register():array
  {
    $formbuilder = \Drupal::formBuilder();
    $form = $formbuilder->getForm('Drupal\wisetalent_user\Form\registerForm');

    return [
      "#theme" => "register",
      "#content" => [
        'google_auth_link'=>'link',
        'form'=>$form
      ]
    ];
  }

  /**
   * @return array
   */
  public function forgot_password():array
  {
    return [
      "#theme" => "forgot_password",
      "#content" => [
        'google_auth_link'=>'link'
      ]
    ];
  }

}
