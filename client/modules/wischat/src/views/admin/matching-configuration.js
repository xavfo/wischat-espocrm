Espo.define('wischat:views/admin/matching-configuration', ['view', 'model'], function (Dep, Model) {

    return Dep.extend({

        template: 'wischat:admin/matching-configuration',

        setup: function () {
            var model = this.model = new Model();
            model.name = 'WischatMatchingConfiguration';
            model.url = 'WischatMatchingConfiguration';
            model.id = '1';

            this.createView('record', 'wischat:views/admin/record/matching-configuration', {
                model: model,
                el: this.getSelector() + ' > .record'
            });
        }
    });
});
