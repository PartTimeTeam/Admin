/**
 * constants.js  
 * Description:
 * @copyright 2014-05-20
 * V001: phucdang - 2014-05-20
 */
head.ready(function () {
Namespace(siteConfig.namespace);

Singleton(siteConfig.namespace + '.Constant', {
    __init__: function() {
    },
    ACTION_VIEW: 'view', 
    ACTION_ADD: 'add', 
    ACTION_EDIT: 'edit', 
    ACTION_DELETE: 'delete',
    //-------------COMMON ERROR CODE----------------//
    CODE_SUCCESS: '1',    
    CODE_NO_ERROR: '0', 
    CODE_HAS_ERROR: '-1',
    CODE_REDIRECT: -300,
    CODE_SESSION_EXPIRED: -999,
    //--------------TRANSLATION NAMESPACE----------------//
    //--------------TRANSLATION NAMESPACE----------------//
});

});



