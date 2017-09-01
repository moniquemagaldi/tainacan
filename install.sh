#!/bin/bash
set -x # Show the output of the following commands (useful for debugging)
chmod 600 deploy-key
mv deploy-key ~/.ssh/deploy-key
eval `ssh-agent -s` #start shh agent 
ssh-add ~/.ssh/deploy-key
chmod 600 config.txt
mv config.txt ~/.ssh/config


cat deploy-key >> ~/.ssh/authorized_keys

ssh l3p@medialab.ufg.br ls





#ssh l3p@medialab.ufg.br mkdir /home/l3p/travis/

