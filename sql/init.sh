#!/bin/sh

docker exec vdmscraper_databases_1 psql -U postgres -c "CREATE USER data WITH PASSWORD 'data';"
docker exec vdmscraper_databases_1 psql -U postgres -c "DROP DATABASE datadb;"
docker exec vdmscraper_databases_1 psql -U postgres -c "CREATE DATABASE datadb;"
docker exec vdmscraper_databases_1 psql -U postgres -c "GRANT ALL PRIVILEGES ON DATABASE datadb to data;"

docker exec vdmscraper_databases_1 psql -U data -d datadb -c "$(cat ../db/data-db/init.sql)"