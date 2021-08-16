rm -rf /temp
cd ./src
php main.php
cd ../temp
if [ ! -f "success" ]; then
  echo -e "\033[31merror\033[0m"
  cd ..
  rm -rf temp/
  exit 0
fi
rm success
cat tldInfo.min.json | jq . > tldInfo.json
cd ..
rm -rf release/
mv temp/ release/
