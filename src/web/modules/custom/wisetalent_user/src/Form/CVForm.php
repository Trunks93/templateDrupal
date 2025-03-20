<?php

namespace Drupal\wisetalent_user\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Formulaire de soumission de CV en front-office.
 */
class CVForm extends FormBase {

  private array $experience_counter = [0];
  /**
   * ID unique du formulaire.
   */
  public function getFormId() {
    return 'cv_form';
  }

  /**
   * Construire le formulaire.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Titre du CV'),
     // '#required' => TRUE,
    ];

    $form['cv_file'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Importer votre CV'),
      '#progress_indicator' => 'throbber',
      '#description' => $this->t('Fichier au format "pdf" ou "docx" de 2Mb Maximum'),
      '#upload_location' => 'public://cv-file/',
      '#upload_validators' => [
        'file_validate_extensions' => ['pdf docx'],
        'file_validate_size' => [2097152], // 2MB
      ],
    ];

    $form['experiences'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Expériences professionnelles'),
      '#prefix' => '<div id="experiences-wrapper">',
      '#suffix' => '</div>',
    ];

      $numberExperiences = $form_state->get('experiences')?? 1;


    $this->_generateExperienceField($form,$numberExperiences);

    $form['experiences']['add_experience'] = [
      '#type' => 'submit',
      '#value' => $this->t('Ajouter une expérience'),
      '#submit' => ['::addExperience'],
      '#attributes' => [
        'class' => ['site-button'],
      ],
      '#ajax' => [
        'callback' => '::ajaxCallback',
        'wrapper' => 'experiences-wrapper',
      ],
    ];




    $form['formations'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Formations'),
      '#prefix' => '<div id="formation-wrapper">',
      '#suffix' => '</div>',
    ];

    $formations = $form_state->get('formations') ?? 1;
    for ($t = 0; $t < $formations; $t++) {
      $form['formations'][$t] = [
        '#type' => 'details',
        '#title' => $this->t('Formation N°@num', ['@num' => $t + 1]),
        '#open' => TRUE,
        'etablissement' => [
          '#type' => 'textfield',
          '#title' => $this->t('Etablissement / Université'),
          //'#required' => TRUE,
        ],
        'diplome' => [
          '#type' => 'textfield',
          '#title' => $this->t('Diplôme / Certitification'),
        ],
        'date_debut' => [
          '#type' => 'date',
          '#title' => $this->t('Date de debut'),
        ],
        'date_fin' => [
          '#type' => 'date',
          '#title' => $this->t('Date de fin'),
        ],
        'remove' => [
          '#type' => 'submit',
          '#value' => $this->t('Supprimer'),
          '#name'=>'remove-'.$t,
          '#attributes' => [
            'class' => ['site-button'],
          ],
          '#submit' => ['::removeExperience'],
          '#submit_data' => [
            'index' => $t,
          ],
          '#ajax' => [
            'callback' => '::ajaxCallback',
            'wrapper' => 'experiences-wrapper',
          ],
        ],
      ];
    }
    $form['formations']['add_formation'] = [
      '#type' => 'submit',
      '#value' => $this->t('Ajouter une formation'),
      '#submit' => ['::addFormation'],
      '#attributes' => [
        'class' => ['site-button'],
      ],
      '#ajax' => [
        'callback' => '::ajaxFormationCallback',
        'wrapper' => 'formation-wrapper',
      ],
    ];


    $form['competences'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Compétences'),
      '#prefix' => '<div id="competence-wrapper">',
      '#suffix' => '</div>',
    ];


    $competences = $form_state->get('competences') ?? 1;
    for ($e = 0; $e < $competences; $e++) {
      $form['competences'][$e] = [
        '#type' => 'details',
        '#title' => $this->t('Compétence N°@num', ['@num' => $e + 1]),
        '#open' => TRUE,
        'niveau_de_maitrise' => [
          '#type' => 'select',
          '#title' => $this->t('Niveau de maîtrise'),
          //'#required' => TRUE,
        ],
        'competence' => [
          '#type' => 'select',
          '#title' => $this->t('Compétences'),
        ],

        'remove' => [
          '#type' => 'submit',
          '#value' => $this->t('Supprimer'),
          '#name'=>'remove-'.$e,
          '#attributes' => [
            'class' => ['site-button'],
          ],
          '#submit' => ['::removeCompetence'],
          '#submit_data' => [
            'index' => $e,
          ],
          '#ajax' => [
            'callback' => '::ajaxCompetenceCallback',
            'wrapper' => 'competences-wrapper',
          ],
        ],
      ];
    }
    $form['competences']['add_competence'] = [
      '#type' => 'submit',
      '#value' => $this->t('Ajouter une compétence'),
      '#submit' => ['::addCompetence'],
      '#attributes' => [
        'class' => ['site-button'],
      ],
      '#ajax' => [
        'callback' => '::ajaxCompetenceCallback',
        'wrapper' => 'competence-wrapper',
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Soumettre le CV'),
    ];

    return $form;
  }

  /**
   * Ajoute une expérience via AJAX.
   */
  public function addExperience(array &$form, FormStateInterface $form_state) {
    $counter = $form_state->get('experiences');
    $form_state->set('experiences', $counter + 1);
    $form_state->setRebuild();
  }

  /**
   * Ajoute une competence via AJAX.
   */
  public function addCompetence(array &$form, FormStateInterface $form_state) {
    $counter = $form_state->get('competences');
    $form_state->set('competences', $counter + 1);
    $form_state->setRebuild();
  }


  /**
   * Ajouter une expérience via AJAX.
   */
  public function removeExperience(array &$form, FormStateInterface $form_state) {
    $button = $form_state->getTriggeringElement();
    $index = $button['#submit_data']['index'];
    $button_name = $form_state->getTriggeringElement()['#name'];
    // Traitement
    $experiences = $form_state->getValue('experiences');

    unset($experiences[$index]);

    // Réindexer le tableau
    $experiences = array_values($experiences);

    // Mise à jour du formulaire

    //$form_state->set('experiences', $form_state->get('experiences') - 1);

    $form_state->setValue('experiences', $experiences);
    $form_state->setRebuild();
  }
  /**
   * Ajouter une formation via AJAX.
   */
  public function addFormation(array &$form, FormStateInterface $form_state) {
    $form_count = $form_state->get('formations');
    $form_state->set('formations', $form_count + 1);
    $form_state->setRebuild();
  }

  /**
   * Callback AJAX pour mettre à jour la liste des expériences.
   */
  public function ajaxCallback(array &$form, FormStateInterface $form_state) {
    return $form['experiences'];
  }

  /**
   * Callback AJAX pour mettre à jour la liste des expériences.
   */
  public function ajaxCompetenceCallback(array &$form, FormStateInterface $form_state) {
    return $form['competences'];
  }

/**
   * Callback AJAX pour mettre à jour la liste des formations.
   */
  public function ajaxFormationCallback(array &$form, FormStateInterface $form_state) {
    return $form['formations'];
  }

  /**
   * Traitement de la soumission du formulaire.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
   // dump($form_state);die();
    $cv = Node::create([
      'type' => 'parcours',
      'title' => $form_state->getValue('title'),
      'status' => 1,
    ]);

    // Gestion des fichiers téléversés
   $fids = $form_state->getValue('cv_file');
    if (!empty($fids[0])) {
      $file = File::load($fids[0]);
      if ($file) {
        $file->setPermanent();
        $file->save();
        $cv->set('field_mon_cv', $file->id());
      }
    }

    // Gestion des expériences sous forme de Paragraphs

    $paragraph_experiences = [];
    array_pop($form_state->getValue('experiences'));
    foreach ($form_state->getValue('experiences') as $experience) {
      if ($experience['poste']) {
        $paragraph = Paragraph::create([
          'type' => 'experience_professionnels',
          'field_date_debut'=>$experience['date_debut'],
          'field_date_fin'=>$experience['date_fin'],
          'field_titre_du_poste' => $experience['poste'],
          'field_entreprise' => $experience['entreprise'],
          'field_description' => $experience['description'],
        ]);
        $paragraph->save();
        $paragraph_experiences[] = $paragraph;
      }
    }


    $paragraph_formations = [];
    array_pop($form_state->getValue('formations'));
    foreach ($form_state->getValue('formations') as $formation) {
      if ($formation['diplome'] && is_string($formation['diplome'])) {
        $paragraph = Paragraph::create([
          'type' => '	formation',
          'field_diplome' => $formation['diplome'],
          'field_etablissement' => $formation['etablissement'],
          'field_date_debut'=>$formation['date_debut'],
          'field_date_fin'=>$formation['date_fin'],
        ]);
        $paragraph->save();
        $paragraph_formations[] = $paragraph;
      }

    }

    if (!empty($paragraph_experiences)) {
      $cv->set('field_experience_professionnels', $paragraph_experiences);
    } else {
      \Drupal::messenger()->addWarning($this->t('Vous devez ajouter au moins une expérience.'));
      return;
    }

    if (!empty($paragraph_formations)) {
      $cv->set('field_formation', $paragraph_formations);
    } else {
      \Drupal::messenger()->addWarning($this->t('Vous devez ajouter au moins une formation.'));
      return;
    }

    $cv->save();
    \Drupal::messenger()->addStatus($this->t('Votre CV a été enregistré avec succès.'));
  }

  public function _generateExperienceField(&$form,$counter){
    for ($i=0; $i< $counter; $i++) {
      $form['experiences'][$i] = [
        '#type' => 'details',
        '#title' => $this->t('Expérience professionnelle N°@num', ['@num' => $i + 1]),
        '#open' => TRUE,
        'poste' => [
          '#type' => 'textfield',
          '#title' => $this->t('Poste occupé'),
          //'#required' => TRUE,
        ],
        'entreprise' => [
          '#type' => 'textfield',
          '#title' => $this->t('Entreprise'),
        ],
        'date_debut' => [
          '#type' => 'date',
          '#title' => $this->t('Date de debut'),
        ],
        'date_fin' => [
          '#type' => 'date',
          '#title' => $this->t('Date de fin'),
        ],
        'description' => [
          '#type' => 'textarea',
          '#title' => $this->t('Description'),
        ],
        'remove' => [
          '#type' => 'submit',
          '#value' => $this->t('Supprimer'),
          '#name'=>'remove-'.$i,
          '#attributes' => [
            'class' => ['site-button'],
          ],
          '#submit' => ['::removeExperience'],
          '#submit_data' => [
            'index' => $i,
          ],
          '#ajax' => [
            'callback' => '::ajaxCallback',
            'wrapper' => 'experiences-wrapper',
          ],
        ],
      ];
    }
  }

  public function _getNiveauDeMaitrise(){

  }

  public function _getCompetenceData(){

  }
}
