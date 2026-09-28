#!/bin/sh
# A local WordPress with MenuDash Theme, the MenuDash plugin and MenuDash's sample menu:
#   dev/serve.sh              http://127.0.0.1:9402 (English)
#   dev/serve.sh 9403 de      on another port, with the site in German
# MenuDash is taken from $MENUDASH_DIR (a copy of github.com/yingshiuan/menudash) or
# downloaded once into dev/.cache. Logged in as admin (password "password").
# One worker: with several, logins get lost.
cd "$(dirname "$0")/.." || exit 1
PORT=${1:-9402}
LANG_CODE=${2:-en}
MD=${MENUDASH_DIR:-dev/.cache/menudash}
if [ ! -d "$MD/menudash" ]; then
	echo "Downloading MenuDash into $MD ..."
	mkdir -p dev/.cache
	curl -sL https://github.com/yingshiuan/menudash/archive/refs/heads/main.tar.gz | tar -xz -C dev/.cache
	mv dev/.cache/menudash-main "$MD"
fi
MD=$(cd "$MD" && pwd)
mkdir -p dev/playground-uploads
BLUEPRINT=dev/blueprint.json
[ "$LANG_CODE" = "de" ] && BLUEPRINT=dev/blueprint-de.json
exec npx -y @wp-playground/cli@latest server \
	--workers=1 \
	--port="$PORT" \
	--mount="$PWD/menudash-theme:/wordpress/wp-content/themes/menudash-theme" \
	--mount="$MD/menudash:/wordpress/wp-content/plugins/menudash" \
	--mount="$MD/sample:/menudash-sample" \
	--mount="$MD/dev:/menudash-dev" \
	--mount="$PWD/dev:/theme-dev" \
	--mount="$PWD/dev/playground-uploads:/wordpress/wp-content/uploads" \
	--blueprint="$PWD/$BLUEPRINT" \
	--login
