define(['siteimproveOverlay'], function (si) {
    'use strict';

    /**
     * Argument passed to most of Siteimprove's native functions
     *
     * @typedef {Object} SiteimproveArgs
     * @property {String} url
     * @property {String} token
     * @property {function} [callback]
     */

    /**
     * Arguments passed to Siteimprove's "clear" function
     *
     * @typedef {Object} SiteimproveClearArgs
     * @property {function} [callback]
     */

    /**
     * Wrapper object that includes function name and arguments for Siteimprove function
     *
     * @typedef {(SiteimproveArgs|SiteimproveClearArgs)} SiteimproveAction
     * @property {String} action
     */

    /**
     * Takes two parameters and an optional callback which will be called when data has been succesfully fetched.
     * It should only be used when in a page specific context in the CMS. The url grabbed from the integration and
     * the token grabbed from the token endpoint. This will request our system to get data for the specific url.
     * In case the Siteimprove Plugin should be shown but we cannot find an url or other scenarios where it makes
     * sense to have it shown but no url, an empty url can be given to input and it will show the Siteimprove
     * Plugin but without any data.
     *
     * @param {SiteimproveArgs} config
     */
    function input(config) {
        si.push(['input', config['url'], config['token'], config['callback']])
    }

    /**
     * Takes two parameters - the url of the domain, the token grabbed from the token endpoint and an optional
     * callback. This should be used when in a context of a site or no site at all. The url should be the indexurl
     * of the site if the integration is able to find a specific site. Otherwise it should send an empty url and
     * little box will be shown and it can be opened to view all sites currently in Siteimprove.
     *
     * @param {SiteimproveArgs} config
     */
    function domain(config) {
        si.push(['domain', config['url'], config['token'], config['callback']])
    }

    /**
     * Takes an optional callback. In case the Siteimprove Plugin should be shown but we cannot find an url or other
     * scenarios where it makes sense to have it shown, this function can be called and it will show the Siteimprove
     * Plugin but without any data.
     *
     * @param {SiteimproveClearArgs} config
     */
    function clear(config) {
        si.push(['clear', config['callback']])
    }

    /**
     * Takes two parameters - the url to recheck, the token grabbed from the token endpoint and an optional callback
     * which will be called when a recheck has been succesfully ordered. This will request a recheck of the url in
     * our system. This is meant to be the publish hook.
     *
     * @param {SiteimproveArgs} config
     */
    function recheck(config) {
        si.push(['recheck', config['url'], config['token'], config['callback']])
    }

    /**
     * Takes two parameters - the url of the page currently viewed (it can also be the domain of the site currently
     * viewed - we will strip it down to the domain either way), the token grabbed from the endpoint and an optional
     * callback which will be called when a recrawl of the site has been succesfully ordered. This will request a
     * recrawl of the entire site in our system. This is meant to be the publish hook for a batch of many pages,
     * e.g. an entire site.
     *
     * @param {SiteimproveArgs} config
     */
    function recrawl(config) {
        si.push(['recrawl', config['url'], config['token'], config['callback']])
    }

    /**
     * This will show an internal log for the script.
     * This way it is possible to see what is going on inside the script.
     */
    function showlog() {
        si.push(['showlog'])
    }

    var actions = {
        'input': input,
        'domain': domain,
        'clear': clear,
        'recheck': recheck,
        'recrawl': recrawl,
        'showlog': showlog
    };

    /**
     * @param {SiteimproveAction} config
     */
    return function (config) {
        var action = config['action'];
        actions[action](config);
    };
});
