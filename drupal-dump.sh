USAGE="Usage: drupal-update.sh project_root drupal_env target_filename"

if [ -n "$1" ]; then
  PROJECT_ROOT=$1
else
  echo $USAGE;
  exit 1;
fi

if [ -n "$2" ]; then
  DRUPAL_ENV=$2
else
  echo $USAGE;
  exit 1;
fi

if [ -n "$3" ]; then
  SQL_FILENAME=$3
else
  echo $USAGE;
  exit 1;
fi

TAR_PATH=/home/vcap

echo "Current working directory is ${PROJECT_ROOT}/web"
COMMAND="../vendor/drush/drush/drush sql-dump @$DRUPAL_ENV --result-file=$TAR_PATH/$SQL_FILENAME"
echo $COMMAND

cd ${PROJECT_ROOT}/web; $COMMAND

cd $TAR_PATH
COMMAND="tar -zcvf $SQL_FILENAME.tar.gz $SQL_FILENAME"
echo $COMMAND

cd ${PROJECT_ROOT}/web; $COMMAND

COMMAND="../vendor/drush/drush/drush fsp s3backups $SQL_FILENAME.tar.gz $SQL_FILENAME.tar.gz"
echo $COMMAND

cd ${PROJECT_ROOT}/web; $COMMAND
