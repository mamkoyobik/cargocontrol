#!/bin/sh
set -eu
BASE="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
SNAP="$BASE/.backups/20260225-054616"
cp -f \
  "$SNAP/index.php" \
  "$SNAP/site-header.php" \
  "$SNAP/section-service-detail.php" \
  "$SNAP/section-gallery.php" \
  "$SNAP/section-contact.php" \
  "$SNAP/section-about.php" \
  "$SNAP/footer.php" \
  "$SNAP/head.php" \
  "$SNAP/style-redesign.css" \
  "$SNAP/js/main.js" \
  "$SNAP/contact-form.php" \
  "$SNAP/handler.php" \
  "$BASE/"
echo "Restored snapshot 20260225-054616"
