#!/bin/bash

# Help menu
print_help() {
cat <<-HELP
Ce script est utilisé pour configurer votre projet automatiquement
Vous devez fournir les informations suivantes:

  1) Le lien du projet sur gitlab (Lien se terminant par .git)

Usage:  bash ${0##*/} --url=URL
Example: (sudo) bash ${0##*/} --url=https://git.smile.ci/projet.git
HELP
exit 0
}

RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m' # No Color

url=${1%/}

# Parse Command Line Arguments
while [ "$#" -gt 0 ]; do
  case "$1" in
    --url=*)
        url="${1#*=}"
        ;;
    --help) print_help;;
    *)
      printf "***********************************************************\n"
      printf "* ${RED}Oops: Vous devez fournir l'url du projet.${NC} *\n"
      printf "***********************************************************\n"
      exit 1
  esac
  shift
done

if [ -z "${url}" ]; then
  printf "*********************************************\n"
  printf "* ${RED}Oops: Vous devez fournir l'url du projet.${NC} *\n"
  printf "*********************************************\n"
  print_help
  exit 1
fi

regex='(https?|git)(://|@)[-A-Za-z0-9\+&@#/%?=~_|!:,.;]*[-A-Za-z0-9\+&@#/%=~_|].git$'

if [[ $url =~ $regex ]]; then 
    
  git remote rename origin upstream

  git remote add origin ${url}

  git checkout -b staging
  git push -u origin staging

  git checkout -b develop
  git push -u origin develop

  printf "* Votre projet est configuré avec 2 nouvelles branches: *\n"
  printf "* ${GREEN}staging${NC}: pour la pré-production *\n"
  printf "* ${GREEN}develop${NC}: pour le developpement *\n"

else
    printf "* ${RED}L'url fournie doit être valide et se terminer par un .git${NC} *\n"
fi

