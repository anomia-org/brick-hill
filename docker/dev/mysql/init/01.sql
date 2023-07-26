/* This file is only run on init startup of the local dbdata docker-compose volume to setup the local db and is never run on prod deploys */

CREATE DATABASE IF NOT EXISTS `brickhill`;