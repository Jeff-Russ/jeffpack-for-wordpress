#!/bin/bash

jslint () {
	local wd="$(pwd)"
	local this_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
	local phpdoc_repo="$this_dir/dependencies/phpDocumentor2"

	local jslint_exec="$phpdoc_repo/bin/jslint"
	
	"$jslint_exec" "$@"
}

phpdoc () {
	local wd="$(pwd)"
	local this_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
	local phpdoc_repo="$this_dir/dependencies/phpDocumentor2"
	
	local templates_dir="$phpdoc_repo/data/templates"
	local phpdoc_exec="$phpdoc_repo/bin/phpdoc"

	mkdir -p "$wd/$1/phpdoc"
	"$phpdoc_exec" -d "$wd/$1" -t "$wd/$1/phpdoc" --ignore="dependencies/" --template="clean"
	# "$phpdoc_exec" -d "$wd/$1" -t "$wd/$1/phpdoc" --template="old-ocean"
	# "$phpdoc_exec" -d "$wd/$1" -t "$wd/$1/phpdoc" --template="responsive"
	# "$phpdoc_exec" -d "$wd/$1" -t "$wd/$1/phpdoc" --template="zend"
	# "$phpdoc_exec" -d "$wd/$1" -t "$wd/$1/phpdoc" --template="checkstyle"
}

validate_json () {
	local wd="$(pwd)"
	local this_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
	local phpdoc_repo="$this_dir/dependencies/phpDocumentor2"

	local validate_json_exec="$phpdoc_repo/bin/validate-json"

	"$validate_json_exec" "$@"
}

echo_dep() {
	local this_dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
	local phpdoc_repo="$this_dir/dependencies/phpDocumentor2"
	echo "$phpdoc_repo"
}
