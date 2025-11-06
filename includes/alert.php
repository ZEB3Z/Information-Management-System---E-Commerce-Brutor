<?php
/**
 * includes/alert.php
 * Modern, reusable flash alert + toast system
 * Compatible with Bootstrap 5
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Render a Bootstrap alert box.
 */
function render_flash($key, $type = 'primary') {
    if (!isset($_SESSION[$key])) return;

    $payload = $_SESSION[$key];
    unset($_SESSION[$key]); // clear it right away

    $content = '';
    if (is_array($payload)) {
        foreach ($payload as $msg) {
            $content .= '<li>' . htmlspecialchars($msg) . '</li>';
        }
        $content = "<ul class='mb-0 ps-3'>{$content}</ul>";
    } else {
        $content = htmlspecialchars($payload);
    }

    echo <<<HTML
    <div class="alert alert-{$type} alert-dismissible fade show shadow-sm mt-3" role="alert">
        {$content}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    HTML;
}

/**
 * Render Bootstrap Toast notifications (bottom-right corner)
 */
function render_toast($key, $type = 'primary', $delay = 4000) {
    if (!isset($_SESSION[$key])) return;

    $payload = $_SESSION[$key];
    unset($_SESSION[$key]);

    $icon = match($type) {
        'success' => '✅',
        'danger' => '❌',
        'warning' => '⚠️',
        'info' => 'ℹ️',
        default => '🔔'
    };

    $content = is_array($payload)
        ? implode('<br>', array_map('htmlspecialchars', $payload))
        : htmlspecialchars($payload);

    echo <<<HTML
    <div class="toast align-items-center text-bg-{$type} border-0 shadow mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="{$delay}">
      <div class="d-flex">
        <div class="toast-body">{$icon} {$content}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
    HTML;
}

/* === STANDARD ALERTS (inline banners) === */
render_flash('success', 'success');
render_flash('message', 'danger');
render_flash('info', 'info');
render_flash('warning', 'warning');

/* === OPTIONAL TOASTS (bottom corner popups) === */
ob_start();
render_toast('toast_success', 'success');
render_toast('toast_error', 'danger');
render_toast('toast_info', 'info');
$toasts = ob_get_clean();

if (!empty($toasts)): ?>
<!-- Toast container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
  <?= $toasts ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const toastElList = [].slice.call(document.querySelectorAll('.toast'));
  toastElList.map(t => new bootstrap.Toast(t).show());
});
</script>
<?php endif; ?>

<!-- Auto-dismiss inline alerts -->
<script>
(function() {
  const AUTO_CLOSE_MS = 6000;
  document.querySelectorAll('.alert-dismissible.fade.show').forEach(alert => {
    setTimeout(() => {
      if (window.bootstrap?.Alert) {
        bootstrap.Alert.getOrCreateInstance(alert).close();
      } else {
        alert.classList.remove('show');
        alert.classList.add('hide');
        setTimeout(() => alert.remove(), 200);
      }
    }, AUTO_CLOSE_MS);
  });
})();
</script>
