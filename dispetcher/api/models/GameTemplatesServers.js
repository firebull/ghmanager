module.exports = {


  migrate: 'safe',
  connection: 'ghmanagerMysqlServer',
  tableName: 'game_templates_servers',
  autoCreatedAt: false,
  autoUpdatedAt: false,
  tables: ['servers', 'game_templates'],

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
      via: 'gametemplates',
      //groupBy: 'servers'
    },

    gameTemplates: {
      columnName: 'game_template_id',
      type: 'integer',
      foreignKey: true,
      references: 'gametemplates',
      on: 'id',
      via: 'servers',
      //groupBy: 'gameTemplates'
    }
  }

};

