#!/bin/bash

mkdir .git/hooks
cp bin/post-update .git/hooks/
cp bin/post-merge .git/hooks/
cp bin/pre-commit .git/hooks/
cp bin/update-composer.sh .git/hooks/
