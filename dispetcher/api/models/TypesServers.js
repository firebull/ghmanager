module.exports = {


  migrate: 'safe',
  connection: 'ghmanagerMysqlServer',
  tableName: 'servers_types',
  autoCreatedAt: false,
  autoUpdatedAt: false,
  tables: ['servers', 'types'],

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
      via: 'types',
      groupBy: 'servers'
    },

    types: {
      columnName: 'type_id',
      type: 'integer',
      foreignKey: true,
      references: 'types',
      on: 'id',
      via: 'servers',
      groupBy: 'types'
    }
  }

};

