<?php

declare(strict_types=1);

namespace Drupal\wisetalent_user\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\image\Entity\ImageStyle;
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


    $build['content'] = [
      '#theme' => 'profile_sidebar',
      '#content'=>[
        'user_display_image'=>$user_display_image,
        'user_small_image'=>$user_small_image,
        'current_user'=>$user->getAccountName(),
        'email_user'=>$user->getEmail()
      ],
      '#cache'=>[
        'max-age' => 0
      ]
    ];
    return $build;
  }

}
