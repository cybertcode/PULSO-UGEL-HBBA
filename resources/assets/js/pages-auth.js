/**
 *  Pages Authentication
 */
'use strict';

document.addEventListener('DOMContentLoaded', function () {
  (() => {
    const formAuthentication = document.querySelector('#formAuthentication');

    // Fix: input-group-merge con pulso-input necesita flex:1 y nowrap
    document.querySelectorAll('.input-group.input-group-merge').forEach(group => {
      group.style.flexWrap = 'nowrap';
      const input = group.querySelector('input');
      if (input) input.style.flex = '1';
    });

    if (formAuthentication && typeof FormValidation !== 'undefined') {
      FormValidation.formValidation(formAuthentication, {
        fields: {
          username: {
            validators: {
              notEmpty: { message: 'Ingresa tu nombre de usuario' },
              stringLength: { min: 6, message: 'El usuario debe tener más de 6 caracteres' }
            }
          },
          email: {
            validators: {
              notEmpty: { message: 'Ingresa tu correo electrónico' },
              emailAddress: { message: 'Ingresa un correo electrónico válido' }
            }
          },
          'email-username': {
            validators: {
              notEmpty: { message: 'Ingresa tu correo o usuario' },
              stringLength: { min: 6, message: 'El usuario debe tener más de 6 caracteres' }
            }
          },
          password: {
            validators: {
              notEmpty: { message: 'Ingresa tu contraseña' },
              stringLength: { min: 6, message: 'La contraseña debe tener más de 6 caracteres' }
            }
          },
          'confirm-password': {
            validators: {
              notEmpty: { message: 'Confirma tu contraseña' },
              identical: {
                compare: () => formAuthentication.querySelector('[name="password"]').value,
                message: 'Las contraseñas no coinciden'
              },
              stringLength: { min: 6, message: 'La contraseña debe tener más de 6 caracteres' }
            }
          },
          terms: {
            validators: {
              notEmpty: { message: 'Debes aceptar los términos y condiciones' }
            }
          }
        },
        plugins: {
          trigger: new FormValidation.plugins.Trigger(),
          bootstrap5: new FormValidation.plugins.Bootstrap5({
            eleValidClass: '',
            rowSelector: '.pulso-field, .mb-6'
          }),
          submitButton: new FormValidation.plugins.SubmitButton(),
          defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
          autoFocus: new FormValidation.plugins.AutoFocus()
        },
        init: instance => {
          instance.on('plugins.message.placed', e => {
            if (e.element && e.element.parentElement && e.element.parentElement.classList.contains('input-group')) {
              const row = e.element.closest('.pulso-field, .mb-6');
              if (row) {
                row.appendChild(e.messageElement);
              } else {
                e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
              }
            }
          });
        }
      });
    }

    // Two Steps — numeral mask
    const numeralMaskElements = document.querySelectorAll('.numeral-mask');
    const formatNumeral = value => value.replace(/\D/g, '');
    if (numeralMaskElements.length > 0) {
      numeralMaskElements.forEach(el => {
        el.addEventListener('input', event => { el.value = formatNumeral(event.target.value); });
      });
    }
  })();
});
