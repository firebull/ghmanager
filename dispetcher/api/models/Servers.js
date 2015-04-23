/**
* GameServer.js
*
* @description :: Model gets data from Servers table
* @docs        :: http://sailsjs.org/#!documentation/models
*/

module.exports = {

    migrate: 'safe',
    connection: 'ghmanagerMysqlServer',
    tableName: 'servers',
    autoCreatedAt: false,
    autoUpdatedAt: false,

    attributes: {
        id: {
            type: 'integer',
            primaryKey: true
        },
        //game_template_id: 'integer',
        address: 'string',
        port: 'integer',
        payedTill: 'datetime',
        initialised: 'integer',
        creationDate: {
            columnName: 'created',
            type: 'datetime',
            defaultsTo: function() {return new Date();}
        },
        updateDate: {
            columnName: 'modified',
            type: 'datetime',
            defaultsTo: function() {return new Date();}
        },

        user: {
          collection: 'Users',
          columnName: 'user_id',
          via: 'servers',
          through: 'serversusers',
          dominant: true
        },

        gameTemplate: {
          collection: 'GameTemplates',
          columnName: 'game_template_id',
          via: 'servers',
          through: 'gametemplatesservers',
          dominant: true
        },

        type: {
          collection: 'Types',
          columnName: 'type_id',
          via: 'servers',
          through: 'typesservers',
          dominant: true
        },

        rootServer: {
          collection: 'RootServers',
          columnName: 'root_server_id',
          via: 'servers',
          through: 'serversrootservers',
          dominant: true
        }
    },

    // Get full server info with minimal checks
    getServerFull: function(req, res){
        var thisRootserverId = sails.config.ghmanager.thisRootserverId;
        var lang = req.getLocale();

        Servers.findOne({id: req.params.id})
               .populate('user')
               .populate('gameTemplate')
               .populate('type')
               .populate('rootServer', {id: thisRootserverId})
               .exec(function (err,found){

                    if (err){
                        console.log(err);
                        res.status(500);
                        return res.json({
                            error: sails.__({
                                    phrase: 'DB error occured. See messages in log',
                                    locale: lang})
                        });
                    } else {
                        if (found.rootServer[0] === undefined){
                            res.status(400);
                            return res.json({
                                error: sails.__({
                                            phrase: 'Requested server is not placed on current RootServer',
                                            locale: lang})
                            });
                        }

                        return res.send(JSON.stringify({
                                          data: found
                                        }, null, 3));
                    }
                });
    },

    // Get full server info with maximum checks
    getServerPayed: function(req, res, result){
        var thisRootserverId = sails.config.ghmanager.thisRootserverId;
        var lang = req.getLocale();

        if (req.params.id === undefined){
            res.status(404);
            return res.json({
                error: sails.__({
                        phrase: 'Server ID is not defined',
                        locale: lang})
            });
        }

        Servers.findOne({id: req.params.id,
                         initialised: 1,
                         payedTill: {'>': new Date()}
                        })
               .populate('user')
               .populate('gameTemplate')
               .populate('type')
               .populate('rootServer', {id: thisRootserverId})
               .exec(function (err, found){

                    if (err){
                        console.log(err);
                        res.status(500);
                        return res.json({
                            error: sails.__({
                                    phrase: 'DB error occured. See messages in log',
                                    locale: lang})
                        });
                    } else {
                        if (found.user[0] === undefined){
                            res.status(400);
                            return res.json({
                                error: sails.__({
                                        phrase: 'Requested server has no owner defined',
                                        locale: lang})
                            });
                        }

                        if (found.gameTemplate[0] === undefined || found.type[0] === undefined){
                            res.status(400);
                            return res.json({
                                error: sails.__({
                                        phrase: 'Requested server has no type or template defined',
                                        locale: lang})
                            });
                        }

                        if (found.rootServer[0] === undefined){
                            res.status(400);
                            return res.json({
                                error: sails.__({
                                        phrase: 'Requested server is not placed on current RootServer',
                                        locale: lang})
                            });
                        }

                        result(null, found);
                    }
                });
    },

};



