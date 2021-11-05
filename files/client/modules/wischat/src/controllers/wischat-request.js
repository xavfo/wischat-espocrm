Espo.define('wischat:controllers/wischat-request', 'controllers/record', function (Dep) {

    return Dep.extend({

        actionSettings: function () {
            var model = this.getSettingsModel();

            model.once('sync', function () {
                model.id = '1';
                this.main('views/settings/edit', {
                    model: model,
                    headerTemplate: 'wischat:admin/header-wischat-settings',
                    recordView: 'wischat:views/admin/wischat-settings'
                });
            }, this);
            model.fetch();
        },

        actionMatchingConfiguration: function () {
            this.main('wischat:views/admin/matching-configuration');
        },

        matchingConfiguration: function (options) {
            this.actionMatchingConfiguration(options);
        },

        getSettingsModel: function () {
            var model = this.getConfig().clone();
            model.defs = this.getConfig().defs;

            return model;
        },

        settings: function (options) {
            this.actionSettings(options);
        },

        listMatching: function (options) {
            this.actionListMatching(options);
        },

    });
});
