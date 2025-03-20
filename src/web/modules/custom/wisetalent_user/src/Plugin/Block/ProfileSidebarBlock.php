<?php

declare(strict_types=1);

namespace Drupal\wisetalent_user\Plugin\Block;

use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Provides a profile sidebar block.
 *
 * @Block(
 *   id = "wisetalent_user_profile_sidebar",
 *   admin_label = @Translation("Profile sidebar"),
 *   category = @Translation("Custom"),
 * )
 */
final class ProfileSidebarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {

    $user = User::load(\Drupal::currentUser()->id());
    if ($user && !$user->user_picture->isEmpty()) {
      $uri = $user->user_picture->entity->getFileUri();
      $user_display_image = \Drupal::service('file_url_generator')->generateAbsoluteString($uri);

      $style = ImageStyle::load('thumbnail');
      $image_uri = $style->buildUri($uri);
      $user_small_image = \Drupal::service('file_url_generator')->generateAbsoluteString($image_uri);
    } else {
      $user_display_image = '';
      $user_small_image = '';
    }

    $query = \Drupal::entityQuery('node')
      ->condition('uid', $user->id()) // Filtre par l'UID de l'utilisateur.
      ->condition('type','parcours')
      ->condition('status', 1) // Facultatif : Filtre les nœuds publiés uniquement.
      ->sort('created', 'DESC') // Tri par date de création, du plus récent au plus ancien.
      ->accessCheck(TRUE);
    // Exécute la requête pour obtenir les IDs des nœuds.
    $parcours = $query->execute();
    if(!empty($parcours)){
     $node = Node::load(current($parcours));
    }

    $build['content'] = [
      '#theme' => 'profile_sidebar',
      '#content'=>[
        'user_display_image'=>$user_display_image,
        'user_small_image'=>$user_small_image,
        'current_user'=>$user->getAccountName(),
        'email_user'=>$user->getEmail(),
        'id_user'=>$user->id(),
        'parcours'=>$node?$node->id():''
      ],
      '#cache'=>[
        'max-age' => 0
      ]
    ];
    return $build;
  }

}
