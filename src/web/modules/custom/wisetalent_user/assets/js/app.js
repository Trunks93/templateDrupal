(function ($, Drupal) {
  Drupal.behaviors.cvForm = {
    attach: function (context, settings) {
      // Prévisualisation en direct
      $('.cv-section input, .cv-section textarea', context).once('cvPreview').on('input', function() {
        updatePreview();
      });

      // Gestion du drag & drop pour la photo
      $('.field--name-field-photo', context).once('cvPhoto').each(function() {
        const dropZone = $(this);

        dropZone.on('dragover', function(e) {
          e.preventDefault();
          $(this).addClass('drag-over');
        });

        dropZone.on('dragleave', function(e) {
          e.preventDefault();
          $(this).removeClass('drag-over');
        });

        dropZone.on('drop', function(e) {
          e.preventDefault();
          $(this).removeClass('drag-over');
          const files = e.originalEvent.dataTransfer.files;
          if (files.length) {
            const fileInput = $(this).find('input[type="file"]');
            fileInput[0].files = files;
            fileInput.trigger('change');
          }
        });
      });

      // Validation avancée des dates
      $('.date-field', context).once('cvDates').on('change', function() {
        validateDates($(this).closest('.experience-item'));
      });

      // Gestion des compétences avec tags
      $('#edit-skills-technical', context).once('cvSkills').each(function() {
        const container = $('<div class="skills-container"></div>');
        const input = $(this);
        const skillsList = $('<div class="skills-list"></div>');

        input.after(container);
        container.append(input).append(skillsList);

        input.on('keydown', function(e) {
          if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addSkill(input.val().trim());
            input.val('');
          }
        });

        // Initialiser avec les compétences existantes
        const existingSkills = input.val().split(',');
        existingSkills.forEach(skill => {
          if (skill.trim()) {
            addSkill(skill.trim());
          }
        });

        function addSkill(skill) {
          if (!skill) return;

          const tag = $(`
            <span class="skill-tag">
              ${skill}
              <button type="button" class="remove-skill">&times;</button>
            </span>
          `);

          skillsList.append(tag);
          updateSkillsInput();

          tag.find('.remove-skill').on('click', function() {
            tag.remove();
            updateSkillsInput();
          });
        }

        function updateSkillsInput() {
          const skills = [];
          skillsList.find('.skill-tag').each(function() {
            skills.push($(this).text().trim());
          });
          input.val(skills.join(', '));
        }
      });

      // Gestion des boutons d'ajout/suppression d'expérience
      $('.add-experience', context).once('cvExperience').on('click', function() {
        // La gestion est déjà faite par Drupal AJAX
        // Ajout d'animations
        setTimeout(() => {
          $('.experience-item').last().hide().slideDown();
        }, 100);
      });

      $('.remove-experience', context).once('cvExperience').on('click', function() {
        $(this).closest('.experience-item').slideUp(function() {
          // La suppression réelle est gérée par Drupal AJAX
        });
      });

      // Fonction de prévisualisation en direct
      function updatePreview() {
        const previewData = {
          personalInfo: {
            firstName: $('#edit-first-name').val(),
            lastName: $('#edit-last-name').val(),
            email: $('#edit-email').val(),
            phone: $('#edit-phone').val()
          },
          summary: $('#edit-summary').val(),
          experiences: [],
          education: [],
          skills: $('#edit-skills-technical').val()
        };

        // Collecter les expériences
        $('.experience-item').each(function() {
          const exp = {
            title: $(this).find('.job-title').val(),
            company: $(this).find('.company').val(),
            dateStart: $(this).find('.date-start').val(),
            dateEnd: $(this).find('.date-end').val(),
            description: $(this).find('.description').val()
          };
          previewData.experiences.push(exp);
        });

        // Collecter les formations
        $('.education-item').each(function() {
          const edu = {
            diploma: $(this).find('.diploma').val(),
            school: $(this).find('.school').val(),
            year: $(this).find('.year').val()
          };
          previewData.education.push(edu);
        });

        // Mettre à jour la prévisualisation
        updatePreviewDisplay(previewData);
      }

      // Fonction de mise à jour de l'affichage de la prévisualisation
      function updatePreviewDisplay(data) {
        const preview = $('.cv-preview');
        if (!preview.length) return;

        preview.html(`
          <div class="preview-header">
            <h2>${data.personalInfo.firstName} ${data.personalInfo.lastName}</h2>
            <p>${data.personalInfo.email} | ${data.personalInfo.phone}</p>
          </div>
          <div class="preview-summary">
            <h3>Résumé</h3>
            <p>${data.summary}</p>
          </div>
          <div class="preview-experiences">
            <h3>Expériences professionnelles</h3>
            ${data.experiences.map(exp => `
              <div class="experience">
                <h4>${exp.title} - ${exp.company}</h4>
                <p>${exp.dateStart} - ${exp.dateEnd}</p>
                <p>${exp.description}</p>
              </div>
            `).join('')}
          </div>
          <div class="preview-education">
            <h3>Formation</h3>
            ${data.education.map(edu => `
              <div class="education">
                <h4>${edu.diploma}</h4>
                <p>${edu.school} - ${edu.year}</p>
              </div>
            `).join('')}
          </div>
          <div class="preview-skills">
            <h3>Compétences</h3>
            <p>${data.skills}</p>
          </div>
        `);
      }

      // Validation des dates
      function validateDates(experienceItem) {
        const dateStart = experienceItem.find('.date-start').val();
        const dateEnd = experienceItem.find('.date-end').val();

        if (dateStart && dateEnd) {
          const start = new Date(dateStart);
          const end = new Date(dateEnd);

          if (start > end) {
            experienceItem.find('.date-error').remove();
            experienceItem.append('<div class="date-error messages messages--error">La date de début doit être antérieure à la date de fin</div>');
            return false;
          }
        }

        experienceItem.find('.date-error').remove();
        return true;
      }
    }
  };
})(jQuery, Drupal);
