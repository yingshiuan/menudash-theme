#!/bin/sh
# The installable theme: dist/menudash-theme.zip, with menudash-theme/ as its only
# top-level folder (what WordPress expects), and no Finder clutter.
#   dev/build-zip.sh
cd "$(dirname "$0")/.." || exit 1
mkdir -p dist
rm -f dist/menudash-theme.zip
zip -rq -X dist/menudash-theme.zip menudash-theme -x "*.DS_Store" -x "*/__MACOSX/*" -x "*/._*"
echo "wrote dist/menudash-theme.zip ($(du -h dist/menudash-theme.zip | cut -f1 | tr -d ' '), version $(sed -n 's/^Version: *//p' menudash-theme/style.css))"
