# Wise sendMail module

## Overview

This module allows you to send html email.
its include a service with send this email.

This module depends on:

- phpmailer_smtp module

## How to use it

- For use this module, you have to install it and his dependencies.
- Enable all modules and configure `phpmailer_smtp`
- In your custom module call the services like this

```bash
 public function functionName(){
    $to = 'email_1@email.com';
    $cc = 'email_2@gmail.com,email_3@gmail.com';
    $subject = 'subject mail';
    $templateHtml = [
      '#theme' => 'your_custom_hook_theme',
      '#content' => [
        'node_title' => 'title',
        'node_url' => 'http://drupal.com',
      ]
    ];
   //the best practice is to call service via dependency injection

    \Drupal::service('wise_sendmail.send')->send($subject, $to, $cc, $templateHtml);
  }
```

In your custom module.
Suppose that your module name is SENDER
in your .module file you will have something like this.

```bash

/**
 * Implements hook_theme().
 */

function sender_theme(): array
{
  return [
    'your_custom_hook_theme' => [
      'render element' => 'elements',
      'variables' => [
        'content' => NULL
      ]
    ],
  ];
}

```
