svn co "https://plugins.svn.wordpress.org/mb-custom-post-type/trunk" svn
cp svn/.svn . -r
rm -rf svn
svn cleanup
svn stat | grep '^?' | awk '{print $2}' | xargs -d "\n" svn add
svn stat | grep '^!' | awk '{print $2}' | xargs -d "\n" svn rm --force
svn ci --no-auth-cache --username rilwis --password $WP_ORG_PASSWORD -m "Version $npm_package_version"
svn cp https://plugins.svn.wordpress.org/mb-custom-post-type/trunk https://plugins.svn.wordpress.org/mb-custom-post-type/tags/$npm_package_version --no-auth-cache --username rilwis --password $WP_ORG_PASSWORD -m "Version $npm_package_version"
