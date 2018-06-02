# This Makefile was taken from the AWS for PHP project with some modifications.
#
# https://github.com/aws/aws-sdk-php/blob/2.7.19/Makefile

test:
	vendor/bin/phpunit $(TEST)

# Ensures that the TAG variable was passed to the make command
check_tag:
	$(if $(TAG),,$(error TAG is not defined. Pass via "make tag TAG=4.2.1"))

# Creates a release but does not push it. This task updates the changelog
# with the TAG environment variable, replaces the VERSION constant, ensures
# that the source is still valid after updating, commits the changelog and
# updated VERSION constant, creates an annotated git tag using chag, and
# prints out a diff of the last commit.
tag: check_tag
	@echo Tagging $(TAG)
	chag update $(TAG)
	sed -i -e "s/const VERSION = '.*'/const VERSION = '$(TAG)'/" src/DTS/eBaySDK/Services/BaseService.php
	php -l src/DTS/eBaySDK/Services/BaseService.php
	git commit -a -m '$(TAG) release'
	chag tag
	@echo "Release has been created. Push using 'make release'"
	@echo "Changes made in the release commit"
	git diff HEAD~1 HEAD

# Creates a release based on the master branch and latest tag. This task
# pushes the latest tag, pushes master, and creates a Github release.
# Use "TAG=X.Y.Z make tag" to create a release, and use "make release"
# to push a release.
release: check_tag 
	git push origin master
	git push origin $(TAG)

# Tags the repo and publishes a release.
full_release: tag release

.PHONY: test
