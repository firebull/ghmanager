/**
 * StatsController
 *
 * @description :: Server-side logic for showing Root Server health
 * @help        :: See http://links.sailsjs.org/docs/controllers
 */

module.exports = {

  /**
   * `StatsController.health()`
   */

  health: function(req, res){
    var result = {'memory': [], 'disk': [], 'errors': []};

    HealthService.memory(function(err, data){
        result.memory = data;

        if (err !== null){
            result.errors.push(err);
        }


        return res.send(JSON.stringify({
                                          health: result
                                        }, null, 3));
    });


  }
};

