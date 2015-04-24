> This instruction is for Version 2 of the GH Mananger panel!

# Software installation
```bash
sudo apt-get install git python-software-properties python g++ make
sudo add-apt-repository ppa:chris-lea/node.js
sudo apt-get update
sudo apt-get install nodejs
ln -s /usr/bin/nodejs /usr/bin/node
sudo npm -g install sails
sudo npm -g install grunt
sudo npm -g install forever
```
# Panel download

```bash    
cd /images
git clone https://github.com/firebull/ghmanager.git   
cd /ghmanager
git checkout v2
ln -s dispetcher /images/dispetcher
ln -s scripts /images/scripts
```    
# Panel dispetcher tuning

    cd /images/dispetcher
    npm install
    npm install sails-mysql

### Parameters

    nano config/connections.js -> Edit MySQL parameters in ghManager section
    nano config/ghmamanager.js -> Add current Server ID and it's AuthKey
    
    nano config/local.js

add this in it:

```javascript
module.exports = {
    environment: 'production',
}
```

## Start Dispetcher

    forever start app.js

    
