#!/bin/bash

if [ -f .update ]; then
	rm .update
fi

if [ -f .gitignore ]; then
	rm .gitignore
fi

if [ -f .releases ]; then
	rm .releases
fi

if [ -f index.php ]; then
	rm index.php
fi

VERSIONS=$(curl "https://raw.githubusercontent.com/codeBodger/Quiz/main/.releases")
vType=("1. unstable (likely non-functional)" "2. alpha    (should be functional, but may be buggy)" "3. beta     (functional, but in need of more testing)" "4. full release")
versions=( )
i=0
for VERSION in $VERSIONS; do
	echo "${vType[i]}"
	versions+=("$VERSION")
	let i++
done
read -p "Choose a version: " v
let v--

FILES=$(curl "https://raw.githubusercontent.com/codeBodger/Quiz/${versions[$v]}/.update")
for FILE in $FILES; do
	cat <<< $(curl "https://raw.githubusercontent.com/codeBodger/Quiz/${versions[$v]}/$FILE") > "$FILE"
done
