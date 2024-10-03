<?php


namespace Drupal\wise_sendmail\Services;


class SendEmail
{

  const MODULE_NAME = 'wise_sendmail';

  /**
   * @param string $subject
   * @param string $to emails separated with coma
   * @param string|null $cc emails separated with coma
   * @param array|null $templateHtml
   * @return bool
   */
  public function send(string $subject, string $to, string $cc = null, array $templateHtml = null): bool
  {
    try {
      $mailManager = \Drupal::service('plugin.manager.mail');
      $langcode = \Drupal::currentUser()->getPreferredLangcode();
      $params = [];
      $params['headers'] = [
        "MIME-Version" => "1.0",
        "Content-Type" => "text/html; charset=UTF-8; format=flowed; delsp=yes",
        "Content-Transfer-Encoding" => "8Bit"
      ];
      $params['id'] = 'mail';
      if ($cc !== null) {
        $params['headers']['Cc'] = $cc;
      }
      if ($templateHtml != null) {
        $params['message'] = \Drupal::service('renderer')->render($templateHtml);
        //$params['message'] = $templateHtml;
      }
      $params['formatter'] = "phpmailer_smtp";
      $params['subject'] = $subject;
      $params['headers']['from'] = \Drupal::config('system.site')->get('mail');

      $message = $mailManager->mail(self::MODULE_NAME, self::MODULE_NAME, $to, $langcode, $params, NULL, false);
      $message['body'] = $params['message'];
      $message['subject'] = $subject;


      $phpMailerSmtp = $mailManager->createInstance('phpmailer_smtp');

      $result['result'] = $phpMailerSmtp->mail($message);

      $this->logSendNotification($result, $subject);
    } catch (\Exception $e) {
      $this->logSendNotification([], $subject, $e);
      $result = false;
      throw new \Exception($e->getMessage());
    }

    return $result['result'];
  }

  /**
   * Logging success or failure sending email
   * @param array $result
   * @param $subject
   * @param null $error
   */
  private function logSendNotification(array $result, $subject, $error = null)
  {
    if ($result['result'] == true) {
      \Drupal::logger('yelen_notification')->info("Notification envoyé pour : " . strtoupper($subject));
    } else {
      if ($error instanceof \Exception) {
        \Drupal::logger('yelen_notification')->critical($error->getMessage());
      } else {
        \Drupal::logger('yelen_notification')->error("Erreur notification pour : " . strtoupper($subject));
      }
    }
  }

}
