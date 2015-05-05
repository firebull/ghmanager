/**
 * keyAuth
 *
 * @module      :: Policy
 * @description :: Simple policy to check auth key from request
 *
 * @docs        :: http://sailsjs.org/#!documentation/policies
 *
 */
module.exports = function(req, res, next) {

  // User is allowed, proceed to the next policy,
  // or if this is the last policy, the controller
  if (req.query['auth'] !== undefined
        && req.query['auth'] == sails.config.ghmanager.authKey) {
    return next();
  }

  // User is not allowed
  // (default res.forbidden() behavior can be overridden in `config/403.js`)
  return res.forbidden('Ivalid Auth, forbidden access');
};
