module.exports = {


  migrate: 'safe',
  connection: 'ghmanagerMysqlServer',
  tableName: 'servers_root_servers',
  autoCreatedAt: false,
  autoUpdatedAt: false,
  tables: ['servers', 'root_servers'],

  junctionTable: true,

  attributes: {

    id: {
      primaryKey: true,
      autoIncrement: true,
      type: 'integer'
    },

    servers: {
      columnName: 'server_id',
      type: 'integer',
      foreignKey: true,
      references: 'servers',
      on: 'id',
      via: 'rootservers',
      groupBy: 'servers'
    },

    rootServers: {
      columnName: 'root_server_id',
      type: 'integer',
      foreignKey: true,
      references: 'rootservers',
      on: 'id',
      via: 'servers',
      groupBy: 'rootServers'
    }
  }

};

