<?php
/* ─────────────────────────────────────────────────────────────
   admin/_footer.php — Shared admin footer partial
   Closes .admin-content, .admin-main, .admin-layout.
   Parent may set $extraScripts (string) for page-specific JS
   loaded BEFORE admin.js (e.g. SortableJS CDN).
   ───────────────────────────────────────────────────────────── */
if (!defined('ADMIN_PAGE')) { http_response_code(403); exit; }
?>
    </div><!-- /.admin-content -->
  </main><!-- /.admin-main -->
</div><!-- /.admin-layout -->

<?= $extraScripts ?? '' ?>
<script src="/assets/js/admin.js?v=20260731c"></script>
</body>
</html>
