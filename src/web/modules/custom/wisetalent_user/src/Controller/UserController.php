<?php


namespace Drupal\wisetalent_user\Controller;

use \Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

  /**
   * Tableau de bord profile utilisateur
   * @return string[]
   */
  public function dashboard(){
    return[
      "#markup"=>'<h2> Tableau de bord</h2>'
    ];
  }

  public function editNodeByUuid($uuid) {
    // Charge le nœud à partir de l'UUID.
    $node = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['uuid' => $uuid]);

    if (!empty($node)) {
      $node = reset($node); // Charge le premier nœud correspondant.
      $nid = $node->id();

      // Redirige vers la page d'édition du nœud.
      return new RedirectResponse(\Drupal\Core\Url::fromRoute('entity.node.edit_form', ['node' => $nid])->toString());
    }

    // Si aucun nœud n'est trouvé, affiche une erreur 404.
    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Aucun nœud trouvé pour cet UUID.');
  }

}
