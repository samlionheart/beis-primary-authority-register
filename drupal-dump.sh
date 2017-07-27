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

cd ${PROJECT_ROOT}/web
COMMAND="../vendor/drush/drush/drush sql-dump @$DRUPAL_ENV --result-file=$TAR_PATH/$SQL_FILENAME"
echo '========================================================================================'
pwd
echo '----------------------------------------------------------------------------------------'
echo $COMMAND
$COMMAND
echo '----------------------------------------------------------------------------------------'
ls -la /home/vcap
echo '----------------------------------------------------------------------------------------'
COMMAND="tar -zcvf $SQL_FILENAME.tar.gz -C $TAR_PATH $SQL_FILENAME"
echo '----------------------------------------------------------------------------------------'
echo $COMMAND
echo '----------------------------------------------------------------------------------------'
$COMMAND
echo '----------------------------------------------------------------------------------------'
COMMAND="../vendor/drush/drush/drush fsp s3backups $SQL_FILENAME.tar.gz $SQL_FILENAME.tar.gz"
echo '========================================================================================'
pwd
echo '----------------------------------------------------------------------------------------'
echo $COMMAND
$COMMAND
echo '========================================================================================'