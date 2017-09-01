#!/bin/bash
set -x # Show the output of the following commands (useful for debugging)
chmod 600 deploy-key
mv deploy-key ~/.ssh/deploy-key
eval `ssh-agent -s` #start shh agent 
ssh-add ~/.ssh/deploy-key
chmod 600 config.txt
mv config.txt ~/.ssh/config


if [ $TRAVIS_BRANCH == 'continuousIntegration' ] ; then
    # Initialize a new git repo in current dir, and push it to our server.
    git init

    git remote add deploy "$SERVER_USER@$SERVER_ADDRESS:$GIT_PATH"
    git config user.name "Travis CI"
    git config user.email "$DEPLOY_EMAIL"

    git add .
    git commit -m "Deploy"
    git push --force deploy continuousIntegration -v
else
    echo "Not deploying, since this branch isn't master."
fi



#cat deploy-key >> ~/.ssh/authorized_keys

#ssh l3p@medialab.ufg.br ls





#ssh l3p@medialab.ufg.br mkdir /home/l3p/travis/

