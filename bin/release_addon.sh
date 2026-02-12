#!/usr/bin/env bash

PLUGIN_DIR=$(basename $PWD)
VERSION=$1
REMOTE="danny@eu1.ibericode.com"
REMOTE_APP_DIR="/var/www/my.boxzillaplugin.com"
PACKAGE_FILE="$HOME/Downloads/$PLUGIN_DIR-$VERSION.zip"

# Exit on errors
set -e

# Check if VERSION argument was supplied
if [ "$#" -lt 1 ]; then
    echo "1 parameters expected, $# found"
    echo "Usage: boxzilla_release <VERSION>"
    exit 1
fi

# Check if we're inside plugin directory
if [ ! -e "$PLUGIN_DIR.php" ]; then
  echo "Plugin entry file not found. Please run this command from inside the $PLUGIN_DIR directory."
  exit 1
fi

# Check if there are uncommitted changes
if [ -n "$(git status --porcelain)" ]; then
  echo "There are uncommitted changes. Please commit those changes before initiating a release."
  exit 1
fi

if [ -e "$PACKAGE_FILE" ]; then
    echo "$PACKAGE_FILE exists. Deleting it."
    rm "$PACKAGE_FILE"
fi

# Build JS & CSS assets
if [[ -e "gulpfile.js" ]]; then
    npx gulp
else
    npm run build
fi

# Update version numbers in code
for FILE in "*.php"; do
    sed -i "s/^Version: .*$/Version: $VERSION/g" $FILE
    sed -i "s/define('\(.*_VERSION\)', '.*');/define('\1', '$VERSION');/g" $FILE
done

# Move up one directory level because we need plugin directory in ZIP file
cd ..

zip -r "$PACKAGE_FILE" "$PLUGIN_DIR" \
	-x "$PLUGIN_DIR/.*" \
	-x "$PLUGIN_DIR/vendor/*" \
	-x "$PLUGIN_DIR/node_modules/*" \
    -x "$PLUGIN_DIR/gulpfile.js" \
	-x "$PLUGIN_DIR/tests" \
	-x "$PLUGIN_DIR/webpack.config*.js" \
    -x "$PLUGIN_DIR/*.json" \
	-x "$PLUGIN_DIR/*.lock" \
	-x "$PLUGIN_DIR/*.xml" \
	-x "$PLUGIN_DIR/assets/src/*"

SIZE=$(stat --printf="%s" "$PACKAGE_FILE")
SIZE_KB=$(echo "$SIZE / 1024" | bc)
echo "$PACKAGE_FILE created ($SIZE_KB KB)"

# Go back into plugin directory
cd "$PLUGIN_DIR"
printf "Push $PACKAGE_NAME to production? [y/N] "
read CONFIRM
if [[ "$CONFIRM" != "y" ]]; then
    exit 1;
fi;

# Copy package to production server
echo "Copying plugin package to production server"
scp "$PACKAGE_FILE" "$REMOTE":"$REMOTE_APP_DIR/var/plugins/$PLUGIN_DIR-$VERSION.zip"

# Update version on production server
echo "Updating plugin version on production server"
ssh -t "$REMOTE" "cd $REMOTE_APP_DIR && bin/console app:plugins:update $PLUGIN_DIR --plugin-version=\"$VERSION\""

# Create git tag
git add . -A
git commit -m "v$VERSION"
git tag "$VERSION"

# Push to git remote
git push origin master
git push origin "tags/$VERSION"
