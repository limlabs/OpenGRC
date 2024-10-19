#!/bin/bash

# Generate SBOM for Composer dependencies
composer make-bom --output-format=json --output-file=bom-composer.json

# Install NPM dependencies
npm install

# Generate SBOM for NPM dependencies
cyclonedx-bom -o bom-node.json

# Merge the two SBOM files into SBOM.cdx in JSON format
cyclonedx merge --input-files bom-composer.json,bom-node.json --output-file SBOM.cdx --output-format json

