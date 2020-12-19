document.addEventListener('DOMContentLoaded', function() {
  // Filter packages
  document.getElementById('filter').addEventListener('keyup', function() {
    var filter = this.value;

    document.getElementById('packages').querySelectorAll('.package').forEach(function(element) {
      if (filter.length > 0) {
        if (element.dataset.name.indexOf(filter) < 0
          && element.dataset.description.indexOf(filter) < 0
          && element.dataset.repositoryUrl.indexOf(filter) < 0
        ) {
          element.style.display = 'none';

          return;
        }
      }

      element.style.display = 'block';
    });
  });

  // Register event to copy to clipboard
  document.querySelectorAll('[data-clipboard]').forEach(function(element) {
    element.addEventListener('click', function() {
      var textarea = document.createElement('textarea');

      textarea.value = element.dataset.clipboard;

      document.body.appendChild(textarea);

      textarea.focus();
      textarea.select();

      try {
        document.execCommand('copy');
      } catch(e) {}

      document.body.removeChild(textarea);
    })
  });

  // Toggle login mask
  if (document.getElementById('login-form-button')) {
    document.getElementById('login-form-button').addEventListener('click', function(event) {
      var mask = document.getElementById('login-mask');

      event.preventDefault();

      mask.classList.toggle('d-none');

      if (!mask.classList.contains('d-none')) {
        document.getElementById('fUsername').focus();
      }

      return false;
    });
  }
});
