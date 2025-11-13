<?php
// includes/messages.php
// Exibe mensagens de sucesso ou erro enviadas via GET, e as remove após 3s.

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

if ($success || $error):
?>
    <div id="flash-message" style="
        color: <?= $success ? '#155724' : '#721c24' ?>;
        background: <?= $success ? '#d4edda' : '#f8d7da' ?>;
        border: 1px solid <?= $success ? '#c3e6cb' : '#f5c6cb' ?>;
        padding: 10px 15px;
        border-radius: 6px;
        margin: 10px 0;
        font-weight: 500;
        transition: opacity 0.5s ease;
    ">
        <?= $success ? '✅ ' . htmlspecialchars($success) : '⚠️ ' . htmlspecialchars($error) ?>
    </div>

    <script>
        setTimeout(() => {
            const msg = document.getElementById('flash-message');
            if (msg) {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 500);
            }

            const url = new URL(window.location);
            url.searchParams.delete('success');
            url.searchParams.delete('error');
            window.history.replaceState({}, document.title, url);
        }, 3000);
    </script>
<?php endif; ?>
