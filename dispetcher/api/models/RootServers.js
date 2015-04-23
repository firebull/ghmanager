/**
* RootServers.js
*
* @description :: Model gets data from RootServers table
* @docs        :: http://sailsjs.org/#!documentation/models
*/

module.exports = {

    migrate: 'safe',
    connection: 'ghmanagerMysqlServer',
    tableName: 'root_servers',
    autoCreatedAt: false,
    autoUpdatedAt: false,

    attributes: {
        id: {
            type: 'integer',
            primaryKey: true
        },
        name: 'string',
        enabled: 'integer',
        slotsMax: 'integer',
        slotsBought: 'integer',

        servers: {
          collection: 'servers',
          columnName: 'server_id',
          via: 'rootServer',
          through: 'serversrootservers',
          //junctionTable: 'types_servers',
          dominant: false
        }

    }

};

